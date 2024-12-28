<template>
    <div class="container mt-4">
        <h1>Gestión de Géneros</h1>
        <button class="btn btn-primary mb-3" @click="openCreateModal">Añadir Género</button>

        <div v-if="genres.length > 0" class="list-group">
            <div v-for="genre in genres" :key="genre.id" class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h5>{{ genre.name }}</h5>
                    <p>Slug: {{ genre.slug }}</p>
                </div>
                <div>
                    <button class="btn btn-warning btn-sm me-2" @click="openEditModal(genre)">Editar</button>
                    <button class="btn btn-danger btn-sm" @click="deleteGenre(genre.id)">Eliminar</button>
                </div>
            </div>
        </div>
        <div v-else>
            <p>No hay géneros disponibles.</p>
        </div>

        <!-- Modal para Crear/Editar -->
        <div class="modal" tabindex="-1" ref="genreModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Editar Género' : 'Añadir Género' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveGenre">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Género</label>
                                <input type="text" id="name" v-model="currentGenre.name" class="form-control" required />
                            </div>
                            <button type="submit" class="btn btn-success">{{ isEditing ? 'Guardar Cambios' : 'Crear' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Modal } from 'bootstrap';

const genres = ref([]);
const currentGenre = ref({ name: '' });
const isEditing = ref(false);
const genreModal = ref(null);
let modalInstance;

const fetchGenres = async () => {
    const response = await fetch('/api/genres');
    genres.value = await response.json();
};

const openCreateModal = () => {
    isEditing.value = false;
    currentGenre.value = { name: '' };
    modalInstance.show();
};

const openEditModal = (genre) => {
    isEditing.value = true;
    currentGenre.value = { ...genre };
    modalInstance.show();
};

const saveGenre = async () => {
    if (isEditing.value) {
        await fetch(`/api/genres/${currentGenre.value.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(currentGenre.value),
        });
    } else {
        await fetch('/api/genres', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(currentGenre.value),
        });
    }
    modalInstance.hide();
    fetchGenres();
};

const deleteGenre = async (id) => {
    if (confirm('¿Estás seguro de eliminar este género?')) {
        await fetch(`/api/genres/${id}`, { method: 'DELETE' });
        fetchGenres();
    }
};

onMounted(() => {
    fetchGenres();
    modalInstance = new Modal(genreModal.value);
});
</script>
