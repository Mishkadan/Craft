function EmailCheck(emailStr) {
	var emailPat = /^(.+)@(.+)$/;
	var specialChars = "\\(\\)<>@,;:\\\\\\\"\\.\\[\\]";
	var validChars = "\[^\\s" + specialChars + "\]";
	var quotedUser = "(\"[^\"]*\")";
	var ipDomainPat = /^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	var atom = validChars + '+';
	var word = "(" + atom + "|" + quotedUser + ")";
	var userPat = new RegExp("^" + word + "(\\." + word + ")*$");
	var domainPat = new RegExp("^" + atom + "(\\." + atom + ")*$");
	var matchArray = emailStr.match(emailPat);
	if(matchArray == null) {
		return false;
	}

	var user = matchArray[1];
	var domain = matchArray[2];

	if(!user || !domain) {
		return false;
	}
	if(user.match(userPat) == null) {
		return false
	}

	var IPArray = domain.match(ipDomainPat);
	if(IPArray != null) {
		// this is an IP address
		for(var i = 1; i <= 4; i++) {
			if(IPArray[i] > 255) {
				//alert("Destination IP address is invalid!")
				return false;
			}
		}
		return true;
	}

	var domainArray = domain.match(domainPat);
	if(domainArray == null) {
		//alert("The domain name doesn't seem to be valid.")
		return false;
	}

	/* domain name seems valid, but now make sure that it ends in a
	 three-letter word (like com, edu, gov) or a two-letter word,
	 representing country (uk, nl), and that there's a hostname preceding
	 the domain or country. */

	/* Now we need to break up the domain to get a count of how many atoms
	 it consists of. */
	var atomPat = new RegExp(atom, "g");
	var domArr = domain.match(atomPat);
	var len = domArr.length;
	if(domArr[domArr.length - 1].length < 2 ||
		domArr[domArr.length - 1].length > 4) {
		// the address must end in a two letter or four letter word.
		//alert("The address must end in a three-letter domain, or two letter country.")
		return false;
	}

	// Make sure there's a host name preceding the domain.
	if(len < 2) {
		var errStr = "This address is missing a hostname!";
		//alert(errStr)
		return false;
	}

	// If we've gotten this far, everything's valid!
	return true;
};

function emailRedrawBS(){
	jQuery('.radio.btn-group label').addClass('btn');
	jQuery(".btn-group label:not(.active)").click(function() {
		var label = jQuery(this);
		var input = jQuery('#' + label.attr('for'));

		if (!input.prop('checked')) {
			label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
			if(input.val()== '') {
					label.addClass('active btn-primary');
			 } else if(input.val()==0) {
					label.addClass('active btn-danger');
			 } else {
			label.addClass('active btn-success');
			 }
			input.prop('checked', true);
		}
	});
	jQuery(".btn-group input[checked=checked]").each(function() {
		if(jQuery(this).val()== '') {
		   jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
		} else if(jQuery(this).val()==0) {
		   jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
		} else {
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
		}
	});
};

// * multiple email
// * ждем полной загрузки документа
document.addEventListener('DOMContentLoaded', () => {

	// * функции
	// * функция валидации email
	// email: строка для проверки на соответствие
	const validateEmail = (email) => {
		// return String(email)
		// 	.toLowerCase()
		// 	.match(
		// 		/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
		// 	);
		// * поддержка unicode
		return email.match(
			/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
		);
	};
	// * проверка строки на JSON
	// string: строка для проверки
	// getJson: true - если строка json, вернет её декодированной, false - true/false
	// getJson: false - если строка, вернет её превращенной в json, false - true/false
	const emailToJson = (string, getJson = true) => {
		try {
			JSON.parse(string);
		} catch (error) {
			return getJson ? JSON.parse(JSON.stringify({0:string})) : false;
		}
		return getJson ? JSON.parse(string) : true;
	}
	// * функция добавления элемента после цели
	// target: DOM элемент, после которого вставляем newElement
	// newElement: новый DOM элемент, который вставляется после target
	const insertAfter = (target, newElement) => {
		target.parentNode.insertBefore(newElement, target.nextSibling);
	}
	// * функция сборки json
	const emailsToJson = () => {
		const allEmailFieldset = document.querySelectorAll('.form__fieldset>.form__input--email');

		// * собираем json
		const json = {};
		allEmailFieldset.forEach((input, index) => {
			const parent = input.parentElement;
			if (validateEmail(parent.dataset.value))
			{
				parent.dataset.order = index;
				if (parent.dataset.value)
				{
					json[index] = parent.dataset.value;
				}
			}
		});
		// * пишем json в скрытое поле для сохранения
		const mainInput = document?.querySelector('.form__input--email[type="hidden"]');
		if (mainInput)
		{
			mainInput.value = JSON.stringify(json);
		}
	}
	// * функция блокировки / разблокировки кнопок удаления
	const switchDisableButtonRemove = (parent) => {
		const allEmailButtonRemove = parent.querySelectorAll('button.form__button--remove');
		if (allEmailButtonRemove.length === 1)
		{
			allEmailButtonRemove[0].disabled = true;
		}
		else
		{
			allEmailButtonRemove.forEach(e => e.disabled = false);
		}

	}
	// * функция добавления нового поля
	// target: DOM элемент, после которого будет добавлен новый новое поле
	// order: ранее сохраненный порядок
	// email: ранее сохраненный email
	const addNewEmailFieldset = (target, order = null, email = null) => {

		const mainInput = target.parentElement.querySelector('input[type="hidden"]');

		// * контейнер, группирующий элементы, связанных с полем ввода email
		const newEmailFieldset = document.createElement('fieldset');
		newEmailFieldset.classList.add('form__fieldset');
		newEmailFieldset.dataset.order = order ?? '';
		newEmailFieldset.dataset.value = email ?? '';

		// * поле ввода email
		const placeholderInput = [
			'info', 'help', 'office', 'moscow', 's.ivanov', 'egor.sviridov'
		];
		const newEmailInput = document.createElement('input');
		newEmailInput.type = 'email';
		newEmailInput.placeholder = `Пример: ${placeholderInput[Math.floor(Math.random()*placeholderInput.length)]}@exemple.com`;
		newEmailInput.value = email ?? '';
		newEmailInput.classList.add('form__input', 'form__input--email');
		newEmailFieldset.append(newEmailInput);

		if (mainInput.hasAttribute('multiple'))
		{
			// * кнопка добавления нового поля, после этого
			const newEmailButtonAdd = document.createElement('button');
			newEmailButtonAdd.type = 'button';
			newEmailButtonAdd.setAttribute('rel', 'tooltip');
			newEmailButtonAdd.title = 'Добавить новый email';
			newEmailButtonAdd.ariaLabel = newEmailButtonAdd.title;
			newEmailButtonAdd.dataset.originalTitle = newEmailButtonAdd.title;
			newEmailButtonAdd.classList.add('form__button', 'form__button--add', 'button', 'button__clear');
			newEmailButtonAdd.insertAdjacentHTML('afterbegin', `<svg class="icon icon__add"><use href="#ui__times-circle"></svg>`);
			newEmailFieldset.append(newEmailButtonAdd);

			// * кнопка удаления
			const newEmailButtonRemove = document.createElement('button');
			newEmailButtonRemove.type = 'button';
			newEmailButtonRemove.setAttribute('rel', 'tooltip');
			newEmailButtonRemove.title = 'Удалить';
			newEmailButtonRemove.ariaLabel = newEmailButtonRemove.title;
			newEmailButtonRemove.dataset.originalTitle = newEmailButtonRemove.title;
			newEmailButtonRemove.classList.add('form__button', 'form__button--remove', 'button', 'button__clear', 'hasTip');
			newEmailButtonRemove.insertAdjacentHTML('afterbegin', `<svg class="icon icon__remove"><use href="#ui__times-circle"></svg>`);
			newEmailFieldset.append(newEmailButtonRemove);

			// * проверка, если кнопка удаления всего 1, блокируем её
			switchDisableButtonRemove(target.parentElement);

			// * событие клика по кнопке добавляет новый контейнер после текущего
			newEmailButtonAdd.addEventListener('click', e => {
				addNewEmailFieldset(e.target.closest('.form__fieldset'));
				// * проверка, если кнопка удаления всего 1, блокируем её
				switchDisableButtonRemove(target.parentElement);
			});

			// * событие клика по кнопке удаления контейнера поля
			newEmailButtonRemove.addEventListener('click', e => {
				// * удаляем контейнер
				e.target.closest('.form__fieldset').remove();
				// * проверка, если кнопка удаления всего 1, блокируем её
				switchDisableButtonRemove(target.parentElement);
				// * сборка json
				emailsToJson();
			});

		}

		// * если включена сортировка
		if (mainInput.hasAttribute('sortable'))
		{
			// * кнопка сортировки
			const newEmailButtonOrder = document.createElement('button');
			newEmailButtonOrder.type = 'button';
			newEmailButtonOrder.setAttribute('rel', 'tooltip');
			newEmailButtonOrder.title = 'Перетащите для сортировки';
			newEmailButtonOrder.ariaLabel = newEmailButtonOrder.title;
			newEmailButtonOrder.dataset.originalTitle = newEmailButtonOrder.title;
			newEmailButtonOrder.classList.add('form__button', 'form__button--order', 'button', 'button__clear', 'hasTip');
			newEmailButtonOrder.insertAdjacentHTML('afterbegin', `<svg class="icon icon__order"><use href="#ui__align-center-v"></svg>`);
			newEmailFieldset.append(newEmailButtonOrder);
		}

		// * добавляем контейнер с полем и кнопками после цели
		insertAfter(target, newEmailFieldset);
		// * сборка json
		emailsToJson();

		// * события
		// * ввод email в поле
		newEmailInput.addEventListener('input', e => {
			newEmailFieldset.dataset.value = e.target.value;
		})
		newEmailInput.addEventListener('change', e => {
			// * если это email
			if (validateEmail(e.target.value))
			{
				e.target.classList.remove('form__input--error');
				// * сборка json
				emailsToJson();
			}
			// * если не email, подсвечиваем поле как ошибочное
			else
			{
				e.target.classList.add('form__input--error');
			}
		})

	}

	// * поле для сохранения email
	const formInputEmail = document?.querySelector('.form__input--email') ?? null;

	if (formInputEmail)
	{
		formInputEmail.type = 'hidden';

		// * проверяем кол-во email
		const emailJson = emailToJson(formInputEmail.value);
		// * если email нет
		if (Object.keys(emailJson).length < 1)
		{
			// * добавляем пустое поле
			addNewEmailFieldset(formInputEmail);
		}
		// * если email есть
		else
		{
			// * строим поля, с реверсом, т.к. новое поле добавляется после скрытого поля, из-за этого порядок полей получается в обратную сторону, .reverse() исправляет это
			for (const [order, email] of Object.entries(emailJson).reverse()) {
				addNewEmailFieldset(formInputEmail, order, email);
			}
		}

		switchDisableButtonRemove(formInputEmail.parentElement);

		// * если включен множественный ввод
		if (formInputEmail.hasAttribute('multiple'))
		{
		}

		// * если включена сортировка полей
		if (formInputEmail.hasAttribute('sortable'))
		{
			// * drag & drop
			new Sortable(formInputEmail.parentElement, {
				animation: 150,
				delay: 150,
				draggable: ".form__fieldset",
				handle: '.form__button--order',
				preventOnFilter: false,
				ghostClass: "sortable--ghost",
				chosenClass: "sortable--chosen",
				dragClass: "sortable--drag",
				// * событие, вызываемое во время перемещения элемента
				onMove: function () {
					// document.body.classList.add('scroll-disable');
					document.getElementsByTagName('html')[0].classList.add('scroll-disable');
				},
				// * событие, вызываемое в начале сортировке
				onStart: function () {
					// document.body.classList.add('scroll-disable');
					document.getElementsByTagName('html')[0].classList.add('scroll-disable');
				},
				// * событие, вызываемое в конце сортировке
				onEnd: function () {
					// document.body.classList.remove('scroll-disable');
					document.getElementsByTagName('html')[0].classList.remove('scroll-disable');
				},
				// * событие, вызываемое по окончанию сортировки элементов
				onUpdate: function (list) {
					// * сборка json
					emailsToJson();
				},
			});
		}
	}

});