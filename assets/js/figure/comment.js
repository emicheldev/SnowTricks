window.addEventListener('DOMContentLoaded', (event) => {
	const figureShow = document.querySelector('body.figure-show');

	if (figureShow) {
		// Suppression d'une photo en ajax
		const linkLoadMoreContent = figureShow.querySelector('.load-more');

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
							const containerComments = figureShow.querySelector(
								'.comments-container .comments'
							);

							if (containerComments) {
								data.forEach((item) => {
									containerComments.innerHTML += item;
								});
							}
						} else {
							alert(data.error);
						}
					})
					.catch((e) => console.error(e))
					.finally(() => {
						linkLoadMoreContent.classList.remove('load');
						const limit = figureShow.querySelector('#limit').value;
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