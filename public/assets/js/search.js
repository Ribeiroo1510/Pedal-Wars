document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    let searchTimeout;

    // Função para realizar a pesquisa
    function searchUsers(query) {
        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            return;
        }

        // Determina o caminho correto baseado na URL atual
        const basePath = window.location.pathname.includes('/views/') ? '../' : '';
        fetch(`${basePath}search_users.php?query=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na pesquisa');
                }
                return response.json();
            })
            .then(users => {
                displaySearchResults(users);
            })
            .catch(error => {
                console.error('Erro na pesquisa:', error);
                searchResults.innerHTML = '<div class="search-error">Erro ao pesquisar usuários</div>';
                searchResults.style.display = 'block';
            });
    }

    // Função para exibir os resultados da pesquisa
    function displaySearchResults(users) {
        if (users.length === 0) {
            searchResults.innerHTML = '<div class="no-results">Nenhum usuário encontrado</div>';
            searchResults.style.display = 'block';
            searchInput.setAttribute('aria-expanded', 'true');
            return;
        }

        let html = '';
        users.forEach(user => {
            html += `
                <div class="search-result-item" data-user-id="${user.id}">
                    <img src="${user.profile_image}" alt="${user.name}" class="result-avatar">
                    <div class="result-info">
                        <div class="result-name">${user.name}</div>
                        <div class="result-stats">
                            ${user.location ? `<span class="result-location">${user.location}</span>` : ''}
                            <span class="result-distance">${user.total_distance} km</span>
                            <span class="result-activities">${user.total_activities} atividades</span>
                        </div>
                    </div>
                </div>
            `;
        });

        searchResults.innerHTML = html;
        searchResults.style.display = 'block';
        searchInput.setAttribute('aria-expanded', 'true');

        // Adicionar event listeners para os resultados
        const resultItems = searchResults.querySelectorAll('.search-result-item');
        resultItems.forEach(item => {
            item.addEventListener('click', function() {
                const userId = this.dataset.userId;
                // Redirecionar para o perfil do usuário
                const currentPath = window.location.pathname;
                if (currentPath.includes('/views/')) {
                    window.location.href = `profile.php?user_id=${userId}`;
                } else {
                    window.location.href = `views/profile.php?user_id=${userId}`;
                }
            });
        });
    }

    // Event listener para o input de pesquisa
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Limpar timeout anterior
        clearTimeout(searchTimeout);
        
        // Se o campo estiver vazio, limpar resultados imediatamente
        if (query.length === 0) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            searchInput.setAttribute('aria-expanded', 'false');
            return;
        }
        
        // Definir novo timeout para evitar muitas requisições
        searchTimeout = setTimeout(() => {
            searchUsers(query);
        }, 300);
    });

    // Esconder resultados quando clicar fora
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.search-container')) {
            searchResults.style.display = 'none';
            searchInput.setAttribute('aria-expanded', 'false');
        }
    });

    // Mostrar resultados quando focar no input (se houver conteúdo)
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && searchResults.innerHTML.trim() !== '') {
            searchResults.style.display = 'block';
        }
    });

    // Navegação com teclado (opcional)
    searchInput.addEventListener('keydown', function(event) {
        const items = searchResults.querySelectorAll('.search-result-item');
        const activeItem = searchResults.querySelector('.search-result-item.active');
        
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (activeItem) {
                activeItem.classList.remove('active');
                const nextItem = activeItem.nextElementSibling;
                if (nextItem) {
                    nextItem.classList.add('active');
                } else {
                    items[0]?.classList.add('active');
                }
            } else {
                items[0]?.classList.add('active');
            }
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (activeItem) {
                activeItem.classList.remove('active');
                const prevItem = activeItem.previousElementSibling;
                if (prevItem) {
                    prevItem.classList.add('active');
                } else {
                    items[items.length - 1]?.classList.add('active');
                }
            } else {
                items[items.length - 1]?.classList.add('active');
            }
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (activeItem) {
                activeItem.click();
            }
        } else if (event.key === 'Escape') {
            searchResults.style.display = 'none';
            searchInput.setAttribute('aria-expanded', 'false');
            this.blur();
        }
    });
});