<template>
    <div class="container mt-4">
        <h1>Gestión de Autores</h1>
        <button class="btn btn-primary mb-3" @click="openCreateModal">Añadir Autor</button>

        <div v-if="authors.length > 0" class="list-group">
            <div v-for="author in authors" :key="author.id" class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h5>{{ author.full_name }}</h5>
                    <p>Alias: {{ author.alias }}</p>
                </div>
                <div>
                    <button class="btn btn-warning btn-sm me-2" @click="openEditModal(author)">Editar</button>
                    <button class="btn btn-danger btn-sm" @click="deleteAuthor(author.id)">Eliminar</button>
                </div>
            </div>
        </div>
        <div v-else>
            <p>No hay autores disponibles.</p>
        </div>

        <!-- Modal para Crear/Editar -->
        <div class="modal" tabindex="-1" ref="authorModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Editar Autor' : 'Añadir Autor' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveAuthor">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nombre Completo</label>
                                <input type="text" id="full_name" v-model="currentAuthor.full_name" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label for="alias" class="form-label">Alias</label>
                                <input type="text" id="alias" v-model="currentAuthor.alias" class="form-control" required />
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

const authors = ref([]);
const currentAuthor = ref({ full_name: '', alias: '' });
const isEditing = ref(false);
const authorModal = ref(null);
let modalInstance;

const fetchAuthors = async () => {
    const response = await fetch('/api/authors');
    authors.value = await response.json();
};

const openCreateModal = () => {
    isEditing.value = false;
    currentAuthor.value = { full_name: '', alias: '' };
    modalInstance.show();
};

const openEditModal = (author) => {
    isEditing.value = true;
    currentAuthor.value = { ...author };
    modalInstance.show();
};

const saveAuthor = async () => {
    if (isEditing.value) {
        await fetch(`/api/authors/${currentAuthor.value.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(currentAuthor.value),
        });
    } else {
        await fetch('/api/authors', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(currentAuthor.value),
        });
    }
    modalInstance.hide();
    fetchAuthors();
};

const deleteAuthor = async (id) => {
    if (confirm('¿Estás seguro de eliminar este autor?')) {
        await fetch(`/api/authors/${id}`, { method: 'DELETE' });
        fetchAuthors();
    }
};

onMounted(() => {
    fetchAuthors();
    modalInstance = new Modal(authorModal.value);
});
</script>
