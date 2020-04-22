window.addEventListener('DOMContentLoaded', (event) => {
	const home = document.querySelector('body.home');

	if (home) {
		// Suppression d'une photo en ajax
		const linkLoadMoreContent = home.querySelector('.actions .load-more');

		if (linkLoadMoreContent) {
			linkLoadMoreContent.addEventListener('click', (e) => {
				e.preventDefault();
				linkLoadMoreContent.classList.add('load');

				fetch(linkLoadMoreContent.getAttribute('href'), {
					method: 'GET',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Content-Type': 'application/json',
					},
				})
					.then((response) => response.json())
					.then((data) => {
						if (data) {
							data = data.html;
							const containerFigures = home.querySelector('.container-figures');

							if (containerFigures) {
								data.forEach((item) => {
									const col = document.createElement('div');
									col.classList.add('col');
									col.innerHTML = item;
									containerFigures.appendChild(col);
								});
							}
						} else {
							alert(data.error);
						}
					})
					.catch((e) => console.error(e))
					.finally(() => {
						linkLoadMoreContent.classList.remove('load');
						const limit = home.querySelector('#limit').value;

						const scrollBottom = home.querySelector('#figures .scroll');

						if (scrollBottom) {
							scrollBottom.classList.remove('hide');
						}

						let href = linkLoadMoreContent.href;
						href = href.split('/index/');
						const index = parseInt(href[1], 10) + 1;

						if (limit >= index) {
							href[1] = index;
							href = href.join('/index/');
							linkLoadMoreContent.href = href;
						} else {
							linkLoadMoreContent.remove();
						}
					});
			});
		}
	}
});