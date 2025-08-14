# Funcionalidade de Pesquisa de Usuários

## Descrição
Foi implementada uma funcionalidade de pesquisa de usuários no header da aplicação, permitindo que os usuários encontrem e visualizem perfis de outros ciclistas.

## Arquivos Modificados/Criados

### Arquivos Criados:
1. **search_users.php** - Endpoint para processar pesquisas de usuários
2. **public/assets/js/search.js** - JavaScript para funcionalidade de pesquisa em tempo real
3. **SEARCH_FEATURE.md** - Esta documentação

### Arquivos Modificados:
1. **views/partials/header.php** - Adicionado container de pesquisa e resultados
2. **public/assets/header.css** - Estilos para a funcionalidade de pesquisa
3. **views/dashboard.php** - Incluído script de pesquisa
4. **views/profile.php** - Modificado para suportar visualização de perfis de outros usuários e incluído script de pesquisa

## Funcionalidades Implementadas

### 1. Pesquisa em Tempo Real
- Pesquisa inicia após digitar 2+ caracteres
- Debounce de 300ms para evitar muitas requisições
- Pesquisa por nome (primeiro nome, último nome ou nome completo)

### 2. Resultados da Pesquisa
- Exibe até 10 resultados
- Mostra foto de perfil, nome, localização, distância total e número de atividades
- Clique no resultado redireciona para o perfil do usuário

### 3. Visualização de Perfis
- Perfil próprio: mostra "Profile" e "Your personal statistics"
- Perfil de outros: mostra "Nome's Profile" e botão "Voltar ao Dashboard"
- Todas as estatísticas são carregadas do banco de dados

### 4. Interface Responsiva
- Design adaptado para dispositivos móveis
- Navegação por teclado (setas, Enter, Escape)
- Hover effects e estados ativos

## Como Usar

1. **Pesquisar Usuários:**
   - Digite no campo de pesquisa no header
   - Aguarde os resultados aparecerem
   - Clique em um resultado para ver o perfil

2. **Visualizar Perfis:**
   - Perfis de outros usuários mostram todas as estatísticas públicas
   - Use o botão "Voltar ao Dashboard" para retornar

## Segurança

- Verificação de autenticação em todas as requisições
- Sanitização de dados de entrada e saída
- Exclusão do usuário atual dos resultados de pesquisa
- Validação de parâmetros GET

## Banco de Dados

A funcionalidade utiliza a tabela `users` existente, pesquisando pelos campos:
- `firstname`
- `lastname` 
- Combinação de ambos

## Estilos CSS

Os estilos seguem o padrão visual existente da aplicação:
- Fonte "Press Start 2P" para consistência
- Cores e bordas similares aos elementos existentes
- Responsividade para diferentes tamanhos de tela