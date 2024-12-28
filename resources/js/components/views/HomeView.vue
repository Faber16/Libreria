<template>
    <div class="container mt-4">
        <h1 class="text-center">¡Bienvenido a la Librería!</h1>
        <p class="text-center">
            Gestiona tus <router-link to="/authors">Autores</router-link>, <router-link to="/books">Libros</router-link>, y <router-link to="/genres">Géneros</router-link>.
        </p>
        <div class="text-center mt-5">
            <i class="bx bx-book bx-lg"></i>
            <i class="bx bx-user bx-lg"></i>
            <i class="bx bx-category bx-lg"></i>
        </div>
    </div>
</template>

<style scoped>
h1 {
    color: #2c3e50;
}

i {
    margin: 0 10px;
    color: #007bff;
}
</style>
