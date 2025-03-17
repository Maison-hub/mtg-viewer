<script setup>
import { onMounted, ref } from 'vue';
import { fetchAllCards } from '../services/cardService';

const page = ref(0);
const cards = ref([]);
const loadingCards = ref(true);

async function loadCards() {
    loadingCards.value = true;
    cards.value = await fetchAllCards(page.value);
    loadingCards.value = false;
}

function nextPage() {
    page.value += 1;
    loadCards(page.value);
}

function prevPage() {
    if (page.value > 1) {
        page.value -= 1;
        loadCards(page.value);
    }
}

onMounted(() => {
    loadCards(0);
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
            <div class="pagination">
                <button type="button" @click="prevPage" :disabled="currentPage === 1">Previous</button>
                <span>Page:  {{ page }}</span>
                <button type="button" @click="nextPage">Next</button>
            </div>
        </div>
    </div>
</template>
