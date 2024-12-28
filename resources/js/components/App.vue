<template>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">Librería</a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <router-link to="/" class="nav-link">Inicio</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/authors" class="nav-link">Autores</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/genres" class="nav-link">Géneros</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/books" class="nav-link">Libros</router-link>
                        </li>
                        <li class="nav-item">
                            <a href="/api/documentation" class="nav-link text-primary">API</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <router-view></router-view>
    </div>
</template>