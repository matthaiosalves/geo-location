<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Uf;
use App\Models\Rodovia;
use App\Models\Trecho;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class DadosController extends Controller
{
    public function buscarDados(Request $request)
    {
        $url = "https://servicos.dnit.gov.br/sgplan/apigeo/rotas/espacializarlinha";
        $url .= "?br={$request->rodovia}&tipo={$request->tipoDeTrecho}&uf={$request->unidadeFederativa}";
        $url .= "&cd_tipo=null&data={$request->dataReferencia}&kmi={$request->kmInicial}&kmf={$request->kmFinal}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);

        if (curl_errno($ch)) {
            return response()->json(['error' => 'Erro no cURL: ' . curl_error($ch)], 500);
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Falha ao decodificar JSON'], 500);
        }

        if (!isset($data['geometry'], $data['properties'])) {
            return response()->json(['error' => 'Dados essenciais estão faltando'], 500);
        }

        $newRequestData = [
            'dataReferencia' => $data['properties']['data'],
            'unidadeFederativa' => $data['properties']['uf'],
            'rodovia' => $data['properties']['br'],
            'kmInicial' => $data['properties']['kmi'],
            'kmFinal' => $data['properties']['kmf'],
            'geometry' => $data['geometry']['coordinates']
        ];

        $this->gravarDados(new Request($newRequestData));

        return response()->json($data['geometry']['coordinates']);
    }


    public function gravarDados(Request $request)
    {
        $uf = Uf::where('uf', $request->unidadeFederativa)->first();
        if (!$uf) {
            return response()->json(['error' => 'Unidade federativa inválida'], 404);
        }

        $rodovia = Rodovia::where('uf_id', $request->rodovia)->first();
        if (!$rodovia) {
            return response()->json(['error' => 'Rodovia inválida'], 404);
        }

        $trecho = new Trecho([
            'data_referencia' => $request->dataReferencia,
            'uf_id' => $uf->id,
            'rodovia_id' => $rodovia->id,
            'quilometragem_inicial' => $request->kmInicial,
            'quilometragem_final' => $request->kmFinal,
            'geo' => json_encode($request->geometry)
        ]);

        try {
            $trecho->save();
            return response()->json([
                'message' => 'Dados salvos com sucesso!',
                'data' => $trecho
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar dados: ' . $e->getMessage()], 500);
        }
    }


    public function listarTrechos(Request $request)
    {
        $trechos = Trecho::query();

        if ($request->filled('dataReferencia')) {
            $trechos->where('data_referencia', $request->dataReferencia);
        }
        if ($request->filled('uf_id')) {
            $trechos->where('uf_id', $request->uf_id);
        }
        if ($request->filled('rodovia_id')) {
            $trechos->where('rodovia_id', $request->rodovia_id);
        }

        return response()->json([
            'trechos' => $trechos->get()
        ]);
    }

    public function excluirTrecho($id)
    {
        $trecho = Trecho::find($id);
        if ($trecho) {
            $trecho->delete();
            return response()->json(['success' => 'Trecho excluído com sucesso.'], 200);
        }

        return response()->json(['error' => 'Trecho não encontrado.'], 404);
    }

    public function atualizarTrecho(Request $request, $id)
    {
        $trecho = Trecho::find($id);
        if (!$trecho) {
            return response()->json(['error' => 'Trecho não encontrado.'], 404);
        }

        $trecho->update($request->all());
        return response()->json(['success' => 'Trecho atualizado com sucesso.', 'trecho' => $trecho], 200);
    }
}
