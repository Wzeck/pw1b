const pokemonName = document.querySelector('.pokemon_name');
const pokemonNumber = document.querySelector('.pokemon_number');
const pokemonImage = document.querySelector('.pokemon_image');

const form = document.querySelector('.form');
const input = document.querySelector('.input_search');
const buttonPrev = document.querySelector('.btn-prev');
const buttonNext = document.querySelector('.btn-next');

// Novos elementos para habilidades e habitat:
let abilitiesList = document.querySelector('.abilities_list');
let habitatField = document.querySelector('.pokemon_habitat');

if (!abilitiesList) {
  abilitiesList = document.createElement('ul');
  abilitiesList.classList.add('abilities_list');
  pokemonNumber.parentNode.insertBefore(abilitiesList, pokemonNumber.nextSibling); // Insere logo após o número
}

if (!habitatField) {
  habitatField = document.createElement('p');
  habitatField.classList.add('pokemon_habitat');
  pokemonNumber.parentNode.insertBefore(habitatField, abilitiesList.nextSibling); // Insere logo após a lista de habilidades
}

let searchPokemon = 1;

async function fetchPokemon(pokemon) {
  try {
    const res = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokemon.toString().toLowerCase()}`);
    if (!res.ok) throw new Error('Pokémon não encontrado');
    const data = await res.json();
    return data;
  } catch {
    return null;
  }
}

async function fetchSpecies(pokemon) {
  try {
    const res = await fetch(`https://pokeapi.co/api/v2/pokemon-species/${pokemon.toString().toLowerCase()}`);
    if (!res.ok) throw new Error('Espécie não encontrada');
    const data = await res.json();
    return data;
  } catch {
    return null;
  }
}

const renderPokemon = async (pokemon) => {

  pokemonName.innerHTML = 'Loading...';
  pokemonNumber.innerHTML = '';
  abilitiesList.innerHTML = '';
  habitatField.textContent = '';

  const data = await fetchPokemon(pokemon);

  if (data) {
    pokemonImage.style.display = 'block';
    pokemonName.innerHTML = data.name;
    pokemonNumber.innerHTML = data.id;

    // Exibe a imagem do sprite animado ou padrão
    const animatedSprite = data['sprites']['versions']['generation-v']['black-white']['animated']['front_default'];
    const defaultSprite = data['sprites']['front_default'];
    pokemonImage.src = animatedSprite || defaultSprite;
    input.value = '';
    searchPokemon = data.id;

    // Exibe as habilidades
    if (data.abilities && Array.isArray(data.abilities)) {
      data.abilities.forEach(a => {
        const li = document.createElement('li');
        li.textContent = a.ability.name.replace(/\-/g, ' ');
        abilitiesList.appendChild(li);
      });
    }

    // Busca e exibe habitat via species API
    const speciesData = await fetchSpecies(pokemon);
    if(speciesData && speciesData.habitat && speciesData.habitat.name) {
      habitatField.textContent = `Habitat: ${speciesData.habitat.name.replace(/\-/g, ' ')}`;
    } else {
      habitatField.textContent = 'Habitat: Desconhecido';
    }

  } else {
    pokemonImage.style.display = 'none';
    pokemonName.innerHTML = 'Not found :c';
    pokemonNumber.innerHTML = '';
    abilitiesList.innerHTML = '';
    habitatField.textContent = '';
  }
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  renderPokemon(input.value.toLowerCase());
});

buttonPrev.addEventListener('click', () => {
  if (searchPokemon > 1) {
    searchPokemon -= 1;
    renderPokemon(searchPokemon);
  }
});

buttonNext.addEventListener('click', () => {
  searchPokemon += 1;
  renderPokemon(searchPokemon);
});

renderPokemon(searchPokemon);