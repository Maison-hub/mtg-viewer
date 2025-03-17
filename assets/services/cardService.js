export async function fetchAllCards() {
    const response = await fetch('/api/card/all');
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCardByName(search, code = null) {
    let url = `/api/card/search?name=${search}`;
    if (code) url += `&code=${code}`;
    const response = await fetch(url);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

export async function fetchSetCodes() {
    const response = await fetch('/api/card/set-codes');
    if (!response.ok) throw new Error('Failed to fetch set codes');
    const result = await response.json();
    return result;
}
