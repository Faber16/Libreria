<template>
    <div class="container mt-4">
        <h1>Gestión de Libros</h1>
        <button class="btn btn-primary mb-3" @click="openCreateModal">Añadir Libro</button>

        <div v-if="books.length > 0" class="list-group">
            <div
                v-for="book in books"
                :key="book.id"
                class="list-group-item d-flex justify-content-between align-items-center"
            >
                <div class="d-flex align-items-center">
                    <img
                        v-if="book.picture !== null"
                        :src="`/api/books/${book.id}/picture`"
                        alt="Imagen del libro"
                        class="img-thumbnail me-3"
                        style="width: 80px; height: 80px; object-fit: cover;"
                    />
                    <div>
                        <h5>{{ book.name }}</h5>
                        <p>
                            Autor: {{ getAuthor(book.author_id) }} | Género: {{ getGenre(book.genre_id) }} |
                            Año: {{ book.year_publication }}
                        </p>
                    </div>
                </div>
                <div>
                    <button class="btn btn-warning btn-sm me-2" @click="openEditModal(book)">
                        Editar
                    </button>
                    <button class="btn btn-info btn-sm me-2" @click="openUploadModal(book.id)">
                        Subir Imagen
                    </button>
                    <button class="btn btn-danger btn-sm" @click="deleteBook(book.id)">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
        <div v-else>
            <p>No hay libros disponibles.</p>
        </div>

        <!-- Modal para Crear/Editar -->
        <div class="modal" tabindex="-1" ref="bookModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Editar Libro' : 'Añadir Libro' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveBook">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Libro</label>
                                <input
                                    type="text"
                                    id="name"
                                    v-model="currentBook.name"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="mb-3">
                                <label for="author_id" class="form-label">ID del Autor</label>
                                <select required v-model="currentBook.author_id" name="author_id" id="author_id" class="form-control">
                                    <option v-for="author in authors" :key="author.id" :value="author.id">{{ author.full_name }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="genre_id" class="form-label">ID del Género</label>
                                <select required v-model="currentBook.genre_id" name="genre_id" id="genre_id" class="form-control">
                                    <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.name }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="year_publication" class="form-label">Año de Publicación</label>
                                <input
                                    type="number"
                                    id="year_publication"
                                    v-model="currentBook.year_publication"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <button type="submit" class="btn btn-success">
                                {{ isEditing ? 'Guardar Cambios' : 'Crear' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Subir Imagen -->
        <div class="modal" tabindex="-1" ref="uploadModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Subir Imagen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="uploadPicture">
                            <div class="mb-3">
                                <label for="picture" class="form-label">Selecciona una imagen</label>
                                <input
                                    type="file"
                                    id="picture"
                                    @change="onFileChange"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <button type="submit" class="btn btn-primary">Subir</button>
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

const books = ref([]);
const genres = ref([]);
const authors = ref([]);
const currentBook = ref({
    name: '',
    author_id: '',
    genre_id: '',
    year_publication: '',
});
const selectedBookId = ref(null);
const isEditing = ref(false);
const bookModal = ref(null);
const uploadModal = ref(null);
let bookModalInstance;
let uploadModalInstance;

const fetchBooks = async () => {
    const response = await fetch('/api/books');
    const booksData = await response.json();
    books.value = await Promise.all(
        booksData.map(async (book) => {
            const pictureResponse = await fetch(`/api/books/${book.id}/picture`);
            return {
                ...book,
                picture: pictureResponse.ok ? pictureResponse.url : null,
            };
        })
    );
};

const openCreateModal = () => {
    isEditing.value = false;
    currentBook.value = {
        name: '',
        author_id: '',
        genre_id: '',
        year_publication: '',
    };
    bookModalInstance.show();
};

const openEditModal = (book) => {
    isEditing.value = true;
    currentBook.value = { ...book };
    bookModalInstance.show();
};

const saveBook = async () => {
    if (isEditing.value) {
        await fetch(`/api/books/${currentBook.value.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(currentBook.value),
        });
    } else {
        const response = await fetch('/api/books', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(currentBook.value),
        });
        const newBook = await response.json();
        books.value.push(newBook);
    }
    bookModalInstance.hide();
    fetchBooks();
};

const deleteBook = async (id) => {
    if (confirm('¿Estás seguro de eliminar este libro?')) {
        await fetch(`/api/books/${id}`, { method: 'DELETE' });
        fetchBooks();
    }
};

const openUploadModal = (bookId) => {
    selectedBookId.value = bookId;
    uploadModalInstance.show();
};

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        currentBook.value.pictureFile = file;
    }
};

const uploadPicture = async () => {
    const formData = new FormData();
    formData.append('image', currentBook.value.pictureFile);

    await fetch(`/api/books/${selectedBookId.value}/picture`, {
        method: 'POST',
        body: formData,
    });

    uploadModalInstance.hide();
    fetchBooks();
};

const fetchAuthors = async () => {
    const response = await fetch('/api/authors');
    authors.value = await response.json();
};

const fetchGenres = async () => {
    const response = await fetch('/api/genres');
    genres.value = await response.json();
};

const getAuthor = (id) => {
    if (authors.value.length === 0) return 'Cargando...';
    for (let i = 0; i < authors.value.length; i++) {
        if (authors.value[i].id === id) {
            return authors.value[i].full_name;
        }
    }
    return 'NN';
};
const getGenre = (id) => {
    if (genres.value.length === 0) return 'Cargando...';

    for (let i = 0; i < genres.value.length; i++) {
        if (genres.value[i].id === id) {
            return genres.value[i].name;
        }
    }
    return 'Desconocido';
};

onMounted(() => {
    fetchAuthors();
    fetchGenres();
    fetchBooks();
    bookModalInstance = new Modal(bookModal.value);
    uploadModalInstance = new Modal(uploadModal.value);
});
</script>

<style scoped>
img {
    border-radius: 5px;
}
</style>
