document.addEventListener('DOMContentLoaded', () => {
    const starElements = document.querySelectorAll('.card-stars[data-repo]');

    starElements.forEach(element => {
        const repo = element.getAttribute('data-repo');
        const countSpan = element.querySelector('.star-count');

        fetch(`https://api.github.com/repos/${repo}`)
            .then(response => {
                if (!response.ok) throw new Error('Error al consultar GitHub API');
                return response.json();
            })
            .then(data => {
                if (data.stargazers_count !== undefined && countSpan) {
                    countSpan.textContent = data.stargazers_count;
                }
            })
            .catch(() => {
                // Si la API falla o supera el límite, mantiene el valor por defecto del HTML
            });
    });
});