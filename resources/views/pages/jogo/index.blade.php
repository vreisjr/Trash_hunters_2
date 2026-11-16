<x-layouts::app :title="'Jogo do Trashinho'">
    @vite(['resources/css/jogo.css', 'resources/js/jogo.js'])

    <div class="trashinho">
        <header class="placar">
            <span>Pontos <strong id="pontos">0</strong></span>
            <span id="estagio">Filhote</span>
            <span id="vidas" aria-label="Vidas">❤️❤️❤️</span>
        </header>

        <div class="palco">
            <canvas id="game" width="360" height="560" aria-label="Jogo do Trashinho"></canvas>

            <div id="overlay" class="overlay">
                <h1>Trashinho tá com fome</h1>
                <p>Alimente o mascote com lixo reciclável. Evite pilhas e baterias!</p>
                <p class="dica">Segure ← → (ou A e D) ou arraste na tela</p>
                <p id="recorde"></p>
                <button id="btn" type="button">Jogar</button>
            </div>
        </div>
    </div>
</x-layouts::app>