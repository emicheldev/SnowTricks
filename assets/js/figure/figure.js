window.addEventListener('DOMContentLoaded', (event) => {
	const figureEditPage = document.querySelector('body.admin-figure-edit');

	if (figureEditPage) {
		const formRemoveFigure = figureEditPage.querySelector('#removeFigure');
		const containerActionsForm = figureEditPage.querySelector('.actions-form');

		const formRemoveMainImg = figureEditPage.querySelector('#removeMainImg');
		const containerActionsMainImg = figureEditPage.querySelector('.actions');

		if (formRemoveFigure && containerActionsForm) {
			containerActionsForm.appendChild(formRemoveFigure);
		}

		if (formRemoveMainImg && containerActionsMainImg) {
			containerActionsMainImg.append(formRemoveMainImg);

			formRemoveMainImg.addEventListener('submit', (e) => {
				const message = confirm(
					"Vous êtes sur le point de supprimer l'image principale de la figure. Êtes vous vraiment sûr ?"
				);
				if (!message) {
					e.preventDefault();
					return;
				}

				return message;
			});
		}
	}
});