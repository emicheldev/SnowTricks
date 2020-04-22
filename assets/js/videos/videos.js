window.addEventListener('DOMContentLoaded', (event) => {
	const figureEdit = document.querySelector('body.admin-figure-edit');

	if (figureEdit) {
		// Suppression d'une photo en ajax
		const linksRemoveVideo = figureEdit.querySelectorAll('.videos [data-delete]');

		if (linksRemoveVideo) {
			linksRemoveVideo.forEach((link) => {
				link.addEventListener('click', (e) => {
					e.preventDefault();
					fetch(link.getAttribute('href'), {
						method: 'DELETE',
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({ _token: link.dataset.token }),
					})
						.then((response) => response.json())
						.then((data) => {
							if (data.success) {
								const videoRemove = link.parentNode.parentNode;

								if (videoRemove) {
									videoRemove.remove();
								}

								const containerVideos = figureEdit.querySelector('.medias .videos');

								if (containerVideos) {
									const allVideos = containerVideos.querySelectorAll('.video');

									if (allVideos && allVideos.length == 0) {
										containerVideos.remove();
									}
								}
							} else {
								alert(data.error);
							}
						})
						.catch((e) => alert(e));
				});
			});
		}
	}
});