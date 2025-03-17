<script setup>
import { ref, onMounted } from 'vue';
import { fetchCardByName, fetchSetCodes } from '../services/cardService';

const loadingCards = ref(false);

const cards = ref([]);
const search = ref('');

const options = ref([]);
const selectedOption = ref('');

const loadOptions = async () => {
    options.value = await fetchSetCodes();
};

onMounted(() => {
    loadOptions();
});

const searchCards = async () => {
    if (search.value.length < 3) {
        cards.value = [];
        return;
    }
    loadingCards.value = true;
    console.log(selectedOption.value);
    cards.value = await fetchCardByName(search.value, selectedOption.value);
    loadingCards.value = false;
};

onMounted(() => {
});

</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
    </div>
    <div class="card-list">
        <label for="search">Rechercher une carte</label>
        <br>
        <input id="search" type="text" v-model="search" placeholder="Rechercher une carte" @input="searchCards">
        <br>
        <div>
            <label for="select">Choose an option:</label>{{ selectedOption }}
            <select id="select" v-model="selectedOption" @change="searchCards">
                <option value="All">All</option>
                <option v-for="option in options" :key="option.setCode" :value="option.setCode">
                    {{ option.setCode }}
                </option>
            </select>
        </div>

        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }"> {{ card.name }} - {{ card.uuid }} </router-link>
            </div>
        </div>
    </div>
</template>
