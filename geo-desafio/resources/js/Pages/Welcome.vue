<template>
    <section class="form shadow">
        <div class="container">
            <form @submit.prevent="buscar" class="row">
                <div class="box row">
                    <div class="col-sm-12 col-md-3 col-lg-3 form-group mb-3">
                        <label for="dataDeReferencia">Data de Referência</label>
                        <input
                            v-model="form.dataReferencia"
                            type="date"
                            class="form-control"
                            id="dataDeReferencia"
                            aria-describedby="dataDeReferencia"
                            placeholder="Data de
                    Referência"
                        />
                    </div>
                    <div class="col-sm-12 col-md-3 col-lg-3 form-group mb-3">
                        <label for="unidadeFederativa"
                            >Unidade da Federação</label
                        >
                        <select
                            v-model="form.unidadeFederativa"
                            class="form-select"
                            aria-label="unidadeFederativa"
                            id="unidadeFederativa"
                        >
                            <option value="" disable selected>
                                Selecione um Estado
                            </option>
                            <option
                                v-for="uf in ufs"
                                :key="uf.id"
                                :value="uf.uf"
                            >
                                {{ uf.uf }}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-3 col-lg-3 form-group mb-3">
                        <label for="rodovia">Rodovia</label>
                        <select
                            v-model="form.rodovia"
                            class="form-select"
                            aria-label="rodovia"
                            id="rodovia"
                        >
                            <option value="" disabled selected>
                                Selecione uma Rodovia
                            </option>
                            <option
                                v-for="rodovia in rodoviasUnicas"
                                :key="rodovia.id"
                                :value="rodovia.rodovia"
                            >
                                {{ rodovia.rodovia }}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-3 col-lg-3 form-group mb-3">
                        <label for="tipoDeTrecho">Tipo de Trecho</label>
                        <select
                            v-model="form.tipoDeTrecho"
                            class="form-select"
                            aria-label="tipoDeTrecho"
                            id="tipoDeTrecho"
                        >
                            <option value="" disable selected>
                                Selecione o tipo
                            </option>
                            <option value="B">B</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-3 col-lg-3 form-group mb-3">
                        <label for="kmInicial">Quilometragem Inicial</label>
                        <input
                            v-model="form.kmInicial"
                            type="number"
                            class="form-control"
                            id="kmInicial"
                            aria-describedby="kmInicial"
                            placeholder="Digite a quilometragem Inicial"
                        />
                    </div>
                    <div class="col-sm-12 col-md-3 col-lg-3 form-group mb-3">
                        <label for="kmFinal">Quilometragem Final</label>
                        <input
                            v-model="form.kmFinal"
                            type="number"
                            class="form-control"
                            id="kmFinal"
                            aria-describedby="kmFinal"
                            placeholder="Digite a quilometragem Final"
                        />
                    </div>
                </div>

                <div
                    class="buttons row gap-2 justify-content-center align-items-center"
                >
                    <button
                        type="submit"
                        class="btn btn-primary col-sm-12 col-md-12 col-lg-12"
                        :disabled="!verificarFormulario"
                    >
                        Buscar
                    </button>
                </div>

                <div class="row mt-4 mb-4">
                    <table class="table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Data</th>
                                <th scope="col">UF</th>
                                <th scope="col">Rodo</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Kmi</th>
                                <th scope="col">Kmf</th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="trecho in trechos" :key="trecho.id">
                                <td>{{ trecho.id }}</td>
                                <td>
                                    {{ formatarData(trecho.data_referencia) }}
                                </td>
                                <td>{{ trecho.uf_id }}</td>
                                <td>{{ trecho.rodovia_id }}</td>
                                <td>B</td>
                                <td>{{ trecho.quilometragem_inicial }}</td>
                                <td>{{ trecho.quilometragem_final }}</td>
                                <td
                                    class="d-flex justify-content-center align-items-center gap-3 mb-3 mt-2"
                                >
                                    <!-- <button
                                        class="btn btn-success"
                                        @click="editarTrecho(trecho)"
                                    >
                                        Editar
                                    </button> -->

                                    <button
                                        class="btn btn-danger"
                                        @click="confirmarExclusao(trecho.id)"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </section>

    <section class="resultado mb-5">
        <div class="container">
            <div class="row">
                <!-- {{ geoJson }} -->
                <div id="map" style="height: 500px"></div>
            </div>
        </div>
    </section>
</template>
<style scoped>
.form {
    max-width: 980px;
    background: grey;
    margin: 25px auto;
    padding: 15px 25px;
    border-radius: 10px;
}
#map {
    height: 100%;
}
</style>
<script>
import axios from "axios";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

export default {
    data() {
        return {
            form: {
                id: "",
                dataReferencia: "",
                unidadeFederativa: "",
                rodovia: "",
                tipoDeTrecho: "",
                kmInicial: "",
                kmFinal: "",
            },
            geoJson: null,
            rodovias: [],
            trechos: [],
            map: null,
        };
    },
    computed: {
        rodoviasUnicas() {
            return this.rodovias.filter(
                (rodovia, index, self) =>
                    index ===
                    self.findIndex((r) => r.rodovia === rodovia.rodovia)
            );
        },
        verificarFormulario() {
            return (
                this.form.dataReferencia &&
                this.form.unidadeFederativa &&
                this.form.rodovia &&
                this.form.tipoDeTrecho &&
                this.form.kmInicial &&
                this.form.kmFinal
            );
        },
    },
    methods: {
        buscarTrechosRegistrados() {
            axios
                .get("/api/trechos")
                .then((response) => {
                    this.trechos = response.data.trechos;
                    console.log("Trechos registrados recebidos:", this.trechos);
                })
                .catch((error) => {
                    console.error(
                        "Erro ao buscar trechos registrados:",
                        error.response
                    );
                });
        },
        buscar() {
            if (this.verificarFormulario) {
                axios
                    .post("/api/buscar-dados", this.form)
                    .then((response) => {
                        console.log("Dados recebidos:", response.data);
                        this.buscarTrechosRegistrados();

                        if (
                            Array.isArray(response.data) &&
                            response.data.length > 0
                        ) {
                            const allCoords = response.data.flat();

                            console.log("Coordenadas processadas:", allCoords);

                            this.geoJson = this.criarGeoJSON(allCoords);
                            console.log("GeoJSON convertido:", this.geoJson);

                            if (
                                this.geoJson &&
                                this.geoJson.features &&
                                this.geoJson.features.length > 0
                            ) {
                                this.atualizarMapa();
                            } else {
                                console.error("GeoJSON inválido ou vazio");
                            }
                        } else {
                            console.error("Dados inválidos recebidos da API");
                        }

                        this.form = {
                            dataReferencia: "",
                            unidadeFederativa: "",
                            rodovia: "",
                            tipoDeTrecho: "",
                            kmInicial: "",
                            kmFinal: "",
                        };
                    })
                    .catch((error) => {
                        console.error(
                            "Erro na requisição do Geo:",
                            error.response
                        );

                        alert(
                            "Não existem informações sobre esse trajeto. Tente outro."
                        );
                    });
            }
        },

        carregarRodovias() {
            axios
                .get("/api/rodovias")
                .then((response) => {
                    this.rodovias = response.data;
                })
                .catch((error) => {
                    console.error("Erro ao buscar rodovias:", error);
                });
        },

        criarGeoJSON(coordinates) {
            return {
                type: "FeatureCollection",
                features: [
                    {
                        type: "Feature",
                        properties: {
                            id: 1,
                        },
                        geometry: {
                            type: "LineString",
                            coordinates: coordinates,
                        },
                    },
                ],
            };
        },

        confirmarExclusao(id) {
            if (confirm("Tem certeza que deseja excluir este trecho?")) {
                axios
                    .delete(`/api/trechos/${id}`)
                    .then((response) => {
                        alert("Trecho excluído com sucesso!");
                        this.trechos = this.trechos.filter((t) => t.id !== id);
                        this.atualizarMapa();
                    })
                    .catch((error) => {
                        console.error(
                            "Erro ao excluir o trecho:",
                            error.response
                        );
                        alert("Erro ao excluir o trecho.");
                    });
            }
        },

        atualizarMapa() {
            if (!this.map) {
                this.map = L.map("map").setView([0, 0], 13);
                L.tileLayer(
                    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                    {
                        maxZoom: 18,
                        attribution: "© OpenStreetMap contributors",
                    }
                ).addTo(this.map);
            }

            // Limpa
            this.map.eachLayer((layer) => {
                if (layer instanceof L.GeoJSON) {
                    this.map.removeLayer(layer);
                }
            });

            // Adiciona o GeoJSON ao mapa
            if (this.geoJson && this.geoJson.features) {
                const geoJsonLayer = L.geoJSON(this.geoJson, {
                    onEachFeature: function (feature, layer) {
                        if (feature.properties) {
                            layer.bindPopup(
                                Object.keys(feature.properties)
                                    .map(
                                        (key) =>
                                            `${key}: ${feature.properties[key]}`
                                    )
                                    .join("<br />")
                            );
                        }
                    },
                }).addTo(this.map);
                this.map.fitBounds(geoJsonLayer.getBounds());
            } else {
                console.error(
                    "GeoJSON inválido ou sem propriedades de 'features'"
                );
            }
        },

        formatarData(data) {
            const novaData = new Date(data);
            const ano = novaData.getFullYear();
            const mes = (novaData.getMonth() + 1).toString().padStart(2, "0");
            const dia = novaData.getDate().toString().padStart(2, "0");
            return `${dia}-${mes}-${ano}`;
        },
    },
    props: {
        ufs: {
            type: Array,
        },
    },
    created() {
        this.carregarRodovias();
        this.buscarTrechosRegistrados();
    },
    mounted() {},
};
</script>
