/*
 ---

 name: MooUpload

 description: Crossbrowser file uploader with HTML5 chunk upload support

 version: 1.1

 license: MIT-style license

 authors:
 - Juan Lago

 requires: [Core/Class, Core/Object, Core/Element.Event, Core/Fx.Elements, Core/Fx.Tween]

 provides: [MooUpload]

 ...
 */


var progressSupport = ('onprogress' in new Browser.Request);

/*
 Extend Request class for allow send binary files

 provides: [Request.sendblob]
 */
Request.implement({

	sendBlob: function(blob) {

		this.options.isSuccess = this.options.isSuccess || this.isSuccess;
		this.running = true;

		var url = String(this.options.url), method = this.options.method.toLowerCase();

		if(!url) url = document.location.pathname;

		var trimPosition = url.lastIndexOf('/');
		if(trimPosition > -1 && (trimPosition = url.indexOf('#')) > -1) url = url.substr(0, trimPosition);

		if(this.options.noCache)
			url += (url.contains('?') ? '&' : '?') + String.uniqueID();

		var xhr = this.xhr;

		if(progressSupport) {
			xhr.onloadstart = this.loadstart.bind(this);
			xhr.onprogress = this.progress.bind(this);
		}

		xhr.open(method.toUpperCase(), url, this.options.async, this.options.user, this.options.password);
		if(this.options.user && 'withCredentials' in xhr) xhr.withCredentials = true;

		xhr.onreadystatechange = this.onStateChange.bind(this);

		Object.each(this.headers, function(value, key) {
			try {
				xhr.setRequestHeader(key, value);
			} catch(e) {
				this.fireEvent('exception', [key, value]);
			}
		}, this);

		this.fireEvent('request');

		xhr.send(blob);

		if(!this.options.async) this.onStateChange();
		if(this.options.timeout) this.timer = this.timeout.delay(this.options.timeout, this);
		return this;
	}
});

var zzz=1;
var GlodObj = {};
var GlodObj2 = {};
/*
 MooUpload class

 provides: [MooUpload]
 */
var MooUpload = new Class({
	Implements: [Options, Events],

	options: {
		action: 'upload.php',
		draggable: true,
		accept: '*/*',
		method: 'auto',
		multiple: true,
		autostart: false,
		listview: true,
		blocksize: 101400,        // I don't recommend you less of 101400 and not more of 502000
		maxuploadspertime: 2,
		minfilesize: 1,
		maxfilesize: 0,
		maxfiles: 0,
		verbose: false,

		flash: {
			movie: 'Moo.Uploader.swf'
		},

		texts: {
			error: 'Error',
			file: 'File',
			filesize: 'Filesize',
			filetype: 'Filetype',
			nohtml5: 'Not support HTML5 file upload!',
			noflash: 'Please install Flash 8.5 or highter version (Have you disabled FlashBlock or AdBlock?)',
			maxselect: 'You can only select a maximum of {maxfiles} files',
			sel: 'Sel.',
			selectfile: 'Add files',
			status: 'Status',
			startupload: 'Start upload',
			uploaded: 'Uploaded',
			deleting: 'Deleting'
		},

		onAddFiles: function() {
		},
		onBeforeUpload: function() {
		},
		onFileDelete: function(fileindex) {
		},
		onFileProgress: function(fileindex, percent) {
		},
		onFileUpload: function(fileindex, response) {
		},
		onFileUploadError: function(fileindex, response) {
		},
		onFinishUpload: function() {
		},
		onLoad: function() {
		},
		onProgress: function(percent, stats) {
		},
		onSelect: function() {
		},
		onSelectError: function(error, filename, filesize) {
		}
	},

	filelist: new Array(),
	lastinput: undefined,
	uploadspertime: 0,
	uploading: true,
	flashobj: null,
	flashloaded: false,

	filenum: 0,


	/*
	 Constructor: initialize
	 Constructor

	 Add event on formular and perform some stuff, you now, like settings, ...
	 */
	initialize: function(container, options) {

		this.container = document.id(container);

		this.setOptions(options);

		// Extend new events
		Object.append(Element.NativeEvents, {dragenter: 2, dragexit: 2, dragover: 2, drop: 2});

		// Call custom method
		this[this.options.method](this.container);

		this.populateFileList(this.container);

	},


	/*
	 Function: baseHtml
	 Private method

	 Deploy standard html
	 */
	baseHtml: function(subcontainer) {

		var subcontainer_id = subcontainer.get('id');

		// Add buttons container
		var btnContainer = new Element('div', {
			'class': 'mooupload_btncontainer'
		}).inject(subcontainer);


		// Add addfile button
		var btnAddFile = new Element('button', {
			id: subcontainer_id + '_btnAddfile',
			html: this.options.texts.selectfile,

			type: 'button',
			'class': 'addfile'
		}).inject(btnContainer);
		this.newInput(subcontainer);


		// Show start upload button
		if(!this.options.autostart) {

			var btnStart = new Element('button', {
				id: subcontainer_id + '_btnbStart',
				html: this.options.texts.startupload,
				type: 'button',
				'class': 'start'
			}).inject(btnContainer);

			btnStart.addEvent('click', function() {
				this.upload(subcontainer);
			}.bind(this));
		}

		var progresscont = new Element('div', {
			'id': subcontainer_id + '_progresscont',
			'class': 'progresscont'
		}).inject(btnContainer);

		new Element('div', {
			id: subcontainer_id + '_progressbar',
			html: '0%',
			'class': 'mooupload_on mooupload_progressbar'
		}).inject(progresscont);

		// Create file list container
		if(this.options.listview) {
			var listview = new Element('div.mooupload_listview', {
				id: subcontainer_id + '_listView'
			}).inject(subcontainer);

			var ulcontainer = new Element('ul').inject(listview);

			const options = this.options;
			const files = options.files;

			// * drag & drop
			new Sortable(ulcontainer, {
				animation: 150,
				delay: 150,
				handle: '.product__thumbnail',
				filter: '.header',
				handle: '.filecont',
				preventOnFilter: false,
				ghostClass: "sortable--ghost",
				chosenClass: "sortable--chosen",
				dragClass: "sortable--drag",
				// * multiDrag
				multiDrag: true,
				selectedClass: 'sortable--selected',
				fallbackTolerance: 3,
				// * событие, вызываемое по окончанию сортировки элементов
				onUpdate: function (list) {

					// * массив из элементов
					const item_list = list.to.querySelectorAll('.item');
					// * объект для передачи, состоит из пар id => order
					const file_list_order = [];

					// * формируем объект для передачи
					for (const [index, item] of item_list.entries())
					{
						// * пока не решил, но order=0 это новые файлы
						const order = index + 1;
						file_list_order.push({
							id: item.dataset.id,
							order
						});
					};

					new Request.JSON({
						url: Cobalt.field_call_url,
						method: 'post',
						field: 'upload',
						autoCancel: true,
						data: {
							field_id: options.field_id,
							func: 'onSaveOrder',
							file_list_order: JSON.stringify(file_list_order),
						},
						onComplete: function(response) {
							if(!response.success) {
								console.log(response.error);
							}
						}
					}).send();

				},
			});

			var header = new Element('li.header').inject(ulcontainer).adopt(



				new Element('div.optionsel', {
					html: this.options.texts.sel
				}),



				/*
				 new Element('div.filetype', {
				 html: this.options.texts.filetype
				 }),
				 */

				new Element('div.filesize', {
					html: this.options.texts.filesize
				})

				//new Element('div.result', {
				//	html: this.options.texts.status
				//})

			);
		}

		this.fireEvent('onLoad');

	},

	// * Максимальный размер загружаемого файла
	allowedFileSize: function (bytes, decimals = 2) {
		if (bytes === 0) return '0 Bytes';

		const k = 1024;
		const dm = decimals < 0 ? 0 : decimals;
		// const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		// * ГОСТ 8.417—2002
		const sizes = ['Байт', 'Кбайт', 'Мбайт', 'Гбайт', 'Тбайт', 'Пбайт', 'Эбайт', 'Збайт', 'Ибайт'];

		const i = Math.floor(Math.log(bytes) / Math.log(k));

		return parseFloat((bytes / Math.pow(k, i)).toFixed(dm))+' '+sizes[i];
	},

	// * замена файла
	replaceFile: function(id) {

		const allFiles = this.options.files;
		const fileReplace = allFiles.filter(x => x.id === id)[0];
		const inputUpload = document.getElementById(`button__upload--${id}`);
		const options = this.options;

		// * файл добавлен для загрузки
		inputUpload.addEventListener('change', e => {

			// * различные проверки
			if (inputUpload.files)
			{
				const fileUpload = inputUpload.files[0];
				const error = [];

				// * очищаем форму, что бы предотвратить дублированную отправку файла
				inputUpload.value = '';

				// * проверяем разрешен ли MIME тип загружаемого файла
				if (!inputUpload.getAttribute('accept')?.includes(fileUpload?.type))
				{
					// * получаем расширение файла
					const ext = /(?:\.([^.]+))?$/;
					error.push(`Файл ${fileUpload.name} нельзя загрузить (тип файла - ${ext.exec(fileUpload.name)[1]}). Выберите разрешенный тип файла: ${this.options.exts.join(', ')}`);
				}
				// * проверяем разрешенный размер загружаемого файла
				if (fileUpload.size > this.options.maxfilesize)
				{
					error.push(`Файл ${fileUpload.name} слишком большой (${this.allowedFileSize(fileUpload.size)}). Файл должен быть меньше ${this.allowedFileSize(this.options.maxfilesize)}`);
				}
				// * если есть ошибки
				if (error.length > 0)
				{
					// * сбрасываем выбранный файл
					fileUpload.value = null;
					// * показываем ошибки
					alert(error.join("\n\n"));
				}

				// * создаем форму для отправки файла на сервер
				const newFormData = new FormData();
				newFormData.append('file', fileUpload, fileUpload.name);
				newFormData.append('old_file', JSON.stringify(fileReplace));
				newFormData.append('field_id', options.field_id);
				newFormData.append('func', 'replaceFile');

				// * создаем запрос для отправки файла на сервер
				const xhr = new XMLHttpRequest();
				xhr.open('post', Cobalt.field_call_url, true);

				// * отправляем форму
				xhr.send(newFormData);

				// * обрабатываем ответ от сервера
				xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && xhr.status == 200) {

						// * получаем ответ от сервера
						const response = xhr?.response ? JSON.parse(xhr.response) : null;

						// * если ответ - успех
						if (response.success)
						{
							// * результат выполнения
							const result = JSON.parse(response.result);

							// * элемент списка
							const item = document.querySelector('li[data-id="'+fileReplace.id+'"]');
							item.id = result.new_filename.replace(/\.[^/.]+$/, '');

							// * превьюшка в списке
							const thumbmini = item.querySelector('img.thumbmini');
							thumbmini.src = '/' + result.new_fullpath;

							// * скрый input
							const inputHidden = item.querySelector('input[type="hidden"]');
							inputHidden.value = result.new_filename;

							// * превьюшка в редакторе
							const thumbindesc = item.querySelector('img.thumbindesc');
							thumbindesc.src = '/' + result.new_fullpath;

							// * меняем параметры файла в this.options.files
							allFiles.forEach((file, i) => {
								if (file.id === id)
								{
									allFiles[i].filename = result.new_filename;
									allFiles[i].realname = result.new_realname;
									allFiles[i].ext = result.new_filename.replace(/\.[^/.]+$/, '');
									allFiles[i].size = result.new_size;
									allFiles[i].width = result.new_width;
									allFiles[i].height = result.new_height;
									allFiles[i].fullpath = result.new_fullpath;
								}
							});
						}
						// * если ошибка
						else
						{
							// * показываем ошибку
							alert(response.result);
						}
					}
				}
				this.options.files = allFiles;
			}
		})

		inputUpload.click();
	},

	htmlAddFile: function(subcontainer) {
		var subcontainer_id = subcontainer.get('id');

		document.id(subcontainer_id + '_btnAddfile').addEvent('click', function(e) {
			e.stop();

			// Check out select max files
			if(this.options.maxfiles && this.countStats().checked >= this.options.maxfiles) {
				this.fireEvent('onSelectError', ['1012', this.filelist[this.filelist.length - 1].name, this.filelist[this.filelist.length - 1].size]);

				return false;
			}

			// Click trigger for input[type=file] only works in FF 4.x, IE and Chrome
			this.lastinput.click();

			this.progressIni(document.id(subcontainer_id + '_progresscont'));

		}.bind(this));
	},

	newInput: function(subcontainer) {

		var subcontainer_id = document.id(subcontainer).get('id');
		var inputsnum = this.countContainers(subcontainer);
		var formcontainer = subcontainer;

		// Hide old input
		if(inputsnum > 0)
			//document.id(subcontainer_id + '_tbxFile' + (inputsnum - 1)).setStyle('display', 'none');


		if(this.options.method == 'html4') {
			formcontainer = new Element('form', {
				id: subcontainer_id + '_frmFile' + inputsnum,
				name: subcontainer_id + '_frmFile' + inputsnum,
				enctype: 'multipart/form-data',
				encoding: 'multipart/form-data',  // I hate IE
				method: 'post',
				action: this.options.action,
				target: subcontainer_id + '_frmFile'
			}).inject(subcontainer);

			if(this.options.maxfilesize > 0) {
				new Element('input', {
					name: 'MAX_FILE_SIZE',
					type: 'hidden',
					value: this.options.maxfilesize
				}).inject(formcontainer);
			}
		}

		// Input File
		this.lastinput = new Element('input', {
			id: subcontainer_id + '_tbxFile' + inputsnum,
			name: subcontainer_id + '_tbxFile' + inputsnum,
			type: 'file',
			size: 1,
			styles: {
				position: 'absolute',
				top: 0,
				left: 0,
				border: 0
			},
			multiple: this.options.multiple,
			accept: this.options.accept

		}).inject(formcontainer);








		/*********Делаем превьюшки*********/

function handleFileSelect(evt) {

    var file = evt.target.files; // FileList object
    var f = file[0];

    if (f.type.match('image.*')) { // Если картинки то...
       // alert("Image only please....");
    	zzz = zzz + 1;
        var reader = new FileReader();
       // Closure to capture the file information.
       reader.onload = (function(theFile) {
         return function(e) {
            // Render thumbnail.
            var span = document.createElement('div');
			span.classList.add('forthumbs-'+ zzz);
			//span.classList.add('forthumb');
            span.innerHTML = ['<img class="thumbmini" title="', escape(theFile.name), '" src="', e.target.result, '" />'].join('');
            document.getElementById('filecont-upl1-'+ zzz).insertBefore(span, null);
			GlodObj.fimg = e.target.result;
			GlodObj.span = span;

        };
    })(f);
    // Read in the image file as a data URL.
    reader.readAsDataURL(f);
		}
		 if (f.type.match('video.*')) { // Если видео, то ...
  var file = event.target.files[0];
  var blobURL = URL.createObjectURL(file);
  var reader = new FileReader();
    // Closure to capture the file information.
    reader.onload = (function(theFile) {
        return function(e) {
            // Render thumbnail.
            var span = document.createElement('div');
			span.classList.add('minivid');
            span.innerHTML = ['<video class="minivid" src="', blobURL, '" controls autoplay muted> Ваш браузер не поддерживает данное видео</video>'].join('');
            document.getElementById('filecont-upl1-'+ zzz).insertBefore(span, null);
      };
    })(f);
    // Read in the image file as a data URL.
    reader.readAsDataURL(f);

	}

}

this.lastinput.addEventListener('change', handleFileSelect, false); // запускаем ф-ю показа превьюшек при загрузке




		// Old version of firefox and opera don't support click trigger for input files fields
		// Internet "Exploiter" do not allow trigger a form submit if the input file field was not clicked directly by the user
		if(this.options.method != 'flash' && (Browser.firefox2 || Browser.firefox3 || Browser.opera || Browser.ie)) {
			this.moveInput(subcontainer);
		}
		else
			this.lastinput.setStyle('visibility', 'hidden');

		// Create events
		this.lastinput.addEvent('change', function(e) {

			e.stop();

			if(this.options.method == 'html4') {
				this.addFiles([
					{
						name: this.getInputFileName(this.lastinput, subcontainer),
						type: null,
						size: null
					}
				], subcontainer);

			}
			else {
				this.addFiles(this.lastinput.files, subcontainer);
			}

		}.bind(this));

		// Hide last input if max selected files
		if(this.options.maxfiles && this.countStats().checked >= this.options.maxfiles)
			this.lastinput.setStyle('display', 'none');

	},

	moveInput: function(subcontainer) {

		// Get addFile attributes
		var btn = subcontainer.getElementById(subcontainer.get('id') + '_btnAddfile');
		var btncoords = btn.getCoordinates(btn.getOffsetParent());

		/*
		 this.lastinput.position({
		 relativeTo: document.id(subcontainer_id+'_btnAddfile'),
		 position: 'bottomLeft'
		 });
		 */

		this.lastinput.setStyles({
			top: btncoords.top,
			left: btncoords.left - 1,
			width: btncoords.width + 2, // Extra space for cover button border
			height: btncoords.height,
			opacity: 0.0001,          // Opera opacity ninja trick
			'-moz-opacity': 0
		});

	},

	upload: function(subcontainer) {

		this.uploading = false;

		this.fireEvent('onBeforeUpload');

		var subcontainer_id = document.id(subcontainer).get('id');

		if(this.options.listview) {
			document.id(subcontainer_id + '_listView').getElements('li.item').addClass('mooupload_readonly');
//      document.id(subcontainer_id+'_listView').getElements('a').setStyle('visibility', 'hidden');
		}

		this.progressStep(document.id(subcontainer_id + '_progresscont'));

		this[this.options.method + 'Upload'](subcontainer);

	},

	progressStep: function(progressbar) {

		if(progressbar.getStyle('display') == 'none')
			progressbar.setStyle('display', 'block');

		var progress = progressbar.getChildren('div');
		var stats = this.countStats();

		stats.uploaded++;
		stats.checked++;

		var percent = (stats.uploaded / stats.checked) * 100;

		progress.set('tween', {duration: 'short'});
		progress.tween('width', percent + '%');

		progress.set('html', percent.ceil() + '%');

		if(percent >= 100) {
			this.uploading = false;
			progress.removeClass('mooupload_on');
			progress.addClass('mooupload_off');
			this.fireEvent('onProgress', [100, stats]);
			this.fireEvent('onFinishUpload');
		}
		else {
			this.fireEvent('onProgress', [percent, stats]);
		}

	},


	progressIni: function(progressbar) {

		var progress = progressbar.getChildren('div');

		progress.removeClass('mooupload_off');
		progress.addClass('mooupload_on');

		progressbar.setStyle('display', 'none');

		progress.setStyle('width', 0);
		progress.set('html', '0%');
	},

	populateFileList: function(maincontainer) {
		var subcontainer = document.id(maincontainer.get('id') + '_listView').getElement('ul');
		var maincontainer_id = maincontainer.get('id');
		var options = this.options;

		var size = 0, key;
		for(key in this.options.files) {
			if(this.options.files.hasOwnProperty(key)) size++;
		}

		if(!size) {
			return;
		}

		if(this.options.maxfiles) {
			this.filesCount = size;
		}
		for(var i = 0, file = null; file = this.options.files[i]; i++) {
			this.filelist[i] = {
				id: String.uniqueID(),
				checked: true,
				name: file.filename,
				type: file.ext,
				size: file.size,
				uploaded: true,
				uploading: false,
				error: false
			};
			this.filenum++;

			var liid = file.filename.toLowerCase();
			liid = liid.replace('.' + file.ext.toLowerCase(), '');

			var elementcontainer = new Element('li', {
				'class': 'item mooupload_readonly',
				id: liid,
				'data-order': file.order ?? 0,
				'data-id': file.id,
			}).inject(subcontainer);



			var optionsel = new Element('div', {
				'class': 'optionsel'
			}).inject(elementcontainer);


			var filecont = new Element('div', {

				'class': 'filecont'
			}).inject(elementcontainer);

			 if (file.ext == 'jpg' || file.ext == 'png' || file.ext == 'gif' || file.ext == 'jpeg' || file.ext == 'bmp') { // Если картинки то...
			filecont.innerHTML = ['<img class="thumbmini" title="', escape(file.realname), '" src="/uploads/gallery/', file.fullpath, '" />'].join('');
			 };
			if (file.ext == 'mp4' || file.ext == 'avi' || file.ext == 'mov' || file.ext == 'mpeg' || file.ext == 'flv' || file.ext == 'ogv') { // Если video то...
			filecont.innerHTML = ['<video class="minivid" src="/uploads/video/', file.fullpath, '" controls autoplay muted> Ваш браузер не поддерживает данное видео</video>'].join('');
            };

			var hiddenInput = new Element('input', {
				'type': 'hidden',
				'class': 'pops',
				'name': this.options.formname,
				'value': file.filename
			}).inject(elementcontainer);

			var f = file;

			//if(this.options.canDelete) {
				var optionremove = new Element('a', {
					'class': 'remove'
				}).inject(optionsel);
				optionremove.innerHTML ="+";


				var func = function(file, j) {
					if(!confirm(this.options.texts.sure)) {
						return;
					}
					$$('#' + file.filename.replace('.' + file.ext, '') + ' div.result').set('html', this.options.texts.deleting).setStyle('background', 'url( "' + this.options.url_root + '/media/mint/js/mooupload/imgs/load_bg_red.gif")').setStyle('color', 'maroon');

					var req = new Request.JSON({
						url: this.options.action_remove_file,
						method: 'post',
						autoCancel: true,
						data: { filename: file.filename },
						onComplete: function(json) {
							if(json.success == 1) {
								$(json.id).slide('out');
								setTimeout(function() {
									$(json.id).destroy();
								}, 500);
								this.filelist[j].checked = false;
							}
							if(json.success == 0) {
								this.fireEvent('onFileDelete', ['1016', file.filename]);
							}
							if(json.success == 2) {
								this.fireEvent('onFileDelete', ['1017', file.filename]);
							}
						}.bind(this)
					}).send();
				};

				optionremove.addEvent('click', func.pass([file, i], this));
		//	}

			var title = file.realname;
			var thumbnail = file.thumbnail;

			if(this.options.allowEditTitle && file.title) {
				title = file.title;
			}

			var css_class = 'filename';
			if(this.options.allowEditTitle) {
				css_class = 'filename  filenameedit';
			}


			var filename_div = new Element('div', {
				'rel': f.id,
				'id': maincontainer.get('id') + '_file' + i,
				'class': css_class,
				'title': this.options.texts.edit_title,
				 html: '<h5>Редактировать название</h5>',
				 name: title
			}).inject(elementcontainer);


			new Element('span', {
				'data-fid': f.id,
				'id': 'id_file' + i,
				'class': 'center',
				html: title,
			}).inject(elementcontainer);


			if(this.options.allowAddDescr) {
				this.addDescriptionInterface(elementcontainer, f.id, f.gurojaya, f.vcolor, f.vsugar, f.vsort, f.vvinogrd, f.vproizvod, f.vorglept, f.vgastronom, f.vtemp, f.vkisl, f.description, f.realname, f.fullpath);

			}

			if(this.options.allowEditTitle) {
				this.addTitleInterface(filename_div, filename_div.get('rel'));
			}

			new Element('div', {
				'class': 'filesize',
				html: this.formatSize(file.size)
				//html: file.fullpath

			}).inject(elementcontainer);

		}

		// * order
		const sortByOrder = Array.from(subcontainer.querySelectorAll('li')).sort((a, b) => {
			return +a.dataset.order - +b.dataset.order;
		});
		subcontainer.set('html', '');
		sortByOrder.forEach(e => e.inject(subcontainer));
	},

	/*
	 Function: addFiles
	 Public method

	 Add new files
	 */
	addFiles: function(files, subcontainer) {

		var subcontainer_id = subcontainer.get('id');
		var maxfileserror = false;

		if(this.options.listview && subcontainer !== undefined)
			var listcontainer = document.id(subcontainer.get('id') + '_listView').getElement('ul');

		for(var i = 0, f; f = files[i]; i++) {

			var fname = f.name || f.fileName;
			var fsize = f.size || f.fileSize;
			var fchecked = true

			// Check out select max files
			if(this.options.maxfiles && this.countStats().checked >= this.options.maxfiles) {
				this.fireEvent('onSelectError', ['1012', fname, fsize]);
				maxfileserror = true;

				fchecked = false;
			}

			var valid = false;
			this.options.exts.each(function(item, index, object) {
				var pat = new RegExp('\.' + item + '$', 'i');
				if(fname.match(pat)) {
					valid = true;
				}
			});
			if(!valid) {
				this.fireEvent('onSelectError', ['1013', fname, fsize]);
				fchecked = false;
				//delete files[i];
				//continue;
			}

			if(fsize != undefined) {

				if(fsize < this.options.minfilesize) {
					this.fireEvent('onSelectError', ['1014', fname, fsize]);
					fchecked = false;
				}

				if(this.options.maxfilesize > 0 && fsize > this.options.maxfilesize) {
					this.fireEvent('onSelectError', ['1015', fname, fsize]);
					fchecked = false;
				}

			}

			this.filelist[this.filelist.length] = {
				id: String.uniqueID(),
				checked: fchecked,
				name: fname,
				type: f.type || f.extension,
				size: fsize,
				uploaded: false,
				uploading: false,
				error: false
			};

			if(this.options.listview && subcontainer !== undefined && fchecked)
			{
				this.addFileList(subcontainer, listcontainer, this.filelist[this.filelist.length - 1]);
			}
		}

//	if (maxfileserror && this.options.texts.maxselect.length > 0)
//			alert(this.options.texts.maxselect.substitute(this.options));



		this.fireEvent('onAddFiles');

		this.newInput(subcontainer);

		if(this.options.autostart)
			this.upload(subcontainer);

	},


	addFileList: function(maincontainer, subcontainer, file) {
zzz = zzz + 1;
		var maincontainer_id = maincontainer.get('id');
//var subcontainer_id = subcontainer.get('id');
		var elementcontainer = new Element('li', {
			'class': 'item', 'id': file.id
		}).inject(subcontainer);

		var optionsel = new Element('div', {
			'class': 'optionsel'
		}).inject(elementcontainer);
		var filecont = new Element('div', {
			id: 'filecont-upl1-'+ zzz,
				'class': 'filecont-upl'
			}).inject(elementcontainer);

		//var filecont2 = new Element('video', {
	       //    id: 'filecont-upl2-'+ zzz,
			//	'class': 'filecont-upl hide',



		//	}).inject(filecont);



		var optiondelete = new Element('a', {
			id: maincontainer_id + '_delete' + this.filelist.length,
			'class': 'delete'
		}).inject(optionsel);
		optiondelete.innerHTML ="+";


		var fileindex = this.filelist.length - 1;

		optiondelete.addEvent('click', function(e) {
			e.stop();

			this.filelist[fileindex].checked = false;

			optiondelete.removeEvents('click');
			optiondelete.getParent('li').destroy();

			this[this.options.method + 'Delete'](fileindex);

			// Check max selected files
			var inputsnum = this.countContainers(maincontainer);

			if(inputsnum > 0) {
				document.id(maincontainer_id + '_tbxFile' + (inputsnum - 1)).setStyles({
					visibility: 'hidden',
					display: 'block'
				});
			}

			this.fireEvent('onFileDelete', [ false, fileindex]);
		}.bind(this));


		new Element('div', {
			'class': 'filename',
			html: file.name,
			styles: {
				//width: '55%'//this.namewidth + 'px',
			}
		}).inject(elementcontainer);
		/*
		 new Element('div', {
		 'class': 'filetype',
		 html: file.type || file.extension || 'n/a'
		 }).inject(elementcontainer);
		 */

		new Element('div', {
			'class': 'filesize',
			html: this.formatSize(file.size)
		}).inject(elementcontainer);

		new Element('div', {
			id: maincontainer_id + '_file_' + this.filelist.length,
			'class': 'result'
		}).inject(elementcontainer);

		elementcontainer.highlight('#FFF', '#E3E3E3');

	},

	formatSize: function(o) {
		if(o === undefined) {
			return "N/A"
		}
		if(o > 1073741824) {
			return (o / 1073741824).toFixed(2) + " GB"
		}
		if(o > 1048576) {
			return (o / 1048576).toFixed(2) + " MB"
		}
		if(o > 1024) {
			return (o / 1024).toFixed(2) + " KB"
		}
		return o + " b"
	},

	getContainers: function(subcontainer) {
		return subcontainer.getElements('input[type=file]');
	},

	getForms: function(subcontainer) {
		return subcontainer.getElements('form');
	},

	countContainers: function(subcontainer) {
		var containers = this.getContainers(subcontainer);

		return containers.length;
	},

	countStats: function() {
		var stats = {
			checked: 0,
			uploaded: 0,
			uploading: 0,
			error: 0
		};

		for(var i = 0, f; f = this.filelist[i]; i++) {
			if(f.checked) {
				stats.checked++;

				stats.uploaded += f.uploaded ? 1 : 0;
				stats.uploading += f.uploading ? 1 : 0;
				stats.error += f.error ? 1 : 0;
			}

		}

		return stats;
	},


	// ------------------------- Specific methods for auto ---------------------

	/*
	 Function: auto
	 Private method

	 Specific method for auto
	 */

	auto: function(subcontainer) {

		// Check html5 support
		if(window.File && window.FileList && window.FileReader && window.Blob) {
			this.options.method = 'html5';

			// Unfortunally Opera 11.11 have an incomplete Blob support
			if(Browser.opera && Browser.version <= 11.11)
				this.options.method = 'auto';
		}

		// Default to html4 if no Flash support
		if(this.options.method == 'auto')
			this.options.method = Browser.Plugins.Flash && Browser.Plugins.Flash.version >= 9 ? 'flash' : 'html4';

		this[this.options.method](subcontainer);

	},

	// ------------------------- Specific methods for flash ---------------------

	/*
	 Function: flash
	 Private method

	 Specific method for flash
	 */
	flash: function(subcontainer) {
		var subcontainer_id = subcontainer.get('id');

		// Check if Flash is supported
		if(!Browser.Plugins.Flash || Browser.Plugins.Flash.version < 9) {
			subcontainer.set('html', this.options.texts.noflash);
			return false;
		}

		this.baseHtml(subcontainer);

		// Translate file type filter
		var filters = this.flashFilter(this.options.accept);

		var btn = subcontainer.getElementById(subcontainer_id + '_btnAddfile');
		var btnposition = btn.getPosition(btn.getOffsetParent());
		var btnsize = btn.getSize();

		// Create container for flash
		var flashcontainer = new Element('div', {
			id: subcontainer_id + '_flash',
			styles: {
				position: 'absolute',
				top: btnposition.y,
				left: btnposition.x
			}
		}).inject(subcontainer);


		// Prevent IE cache bug
		if(Browser.ie)
			this.options.flash.movie += (this.options.flash.movie.contains('?') ? '&' : '?') + 'mooupload_movie=' + Date.now();


		// Deploy flash movie
		this.flashobj = new Swiff(this.options.flash.movie, {
			container: flashcontainer.get('id'),
			width: btnsize.x,
			height: btnsize.y,
			params: {
				wMode: 'transparent',
				bgcolor: '#000000'
			},
			callBacks: {

				load: function() {

					Swiff.remote(this.flashobj.toElement(), 'xInitialize', {
						multiple: this.options.multiple,
						url: this.options.action,
						method: 'post',
						queued: this.options.maxuploadspertime,
						fileSizeMin: this.options.fileminsize,
						fileSizeMax: this.options.filemaxsize ? this.options.filemaxsize : null,
						maxFiles: this.options.maxfiles,
						typeFilter: filters,
						mergeData: true,
						data: this.cookieData(),
						verbose: this.options.verbose
					});

					this.flashloaded = true;

				}.bind(this),

				select: function(files) {
					this.addFiles(files[0], subcontainer);
					this.progressIni(document.id(subcontainer_id + '_progresscont'));

				}.bind(this),

				complete: function(resume) {
					this.uploading = false;
				}.bind(this),

				fileProgress: function(file) {

					this.fireEvent('onFileProgress', [file[0].id, file[0].progress.percentLoaded]);

					if(this.options.listview) {
						var respcontainer = document.id(subcontainer_id + '_file_' + file[0].id);

						respcontainer.set('html', file[0].progress.percentLoaded + '%');
					}

				}.bind(this),

				fileComplete: function(file) {

					this.filelist[file[0].id - 1].uploaded = true;

					this.fireEvent('onFileProgress', [file[0].id, 100]);

					if(this.options.listview) {

						var respcontainer = document.id(subcontainer_id + '_file_' + file[0].id);

						if(file[0].response.error > 0) {
							respcontainer.addClass('mooupload_error');
							respcontainer.set('html', this.options.texts.error);
						}
						else {
							respcontainer.addClass('mooupload_noerror');
							respcontainer.set('html', this.options.texts.uploaded);

						}
					}

					this.progressStep(document.id(subcontainer_id + '_progresscont'));

					this.fireEvent('onFileUpload', [file[0].id, JSON.decode(file[0].response.text)]);

				}.bind(this),

				maxFilesError: function() {

					this.fireEvent('onSelectError', ['1012', this.filelist[this.filelist.length - 1].name, this.filelist[this.filelist.length - 1].size]);

//			if (this.options.texts.maxselect.length > 0)
//				alert(this.options.texts.maxselect.substitute(this.options));


				}.bind(this)

			}
		});






		// toElement() method doesn't work in IE
		/*
		 var flashElement = this.flashobj.toElement();

		 // Check flash load
		 if (!flashElement.getParent() || flashElement.getStyle('display') == 'none')
		 {
		 subcontainer.set('html', this.options.texts.noflash);
		 return false;
		 }
		 */

	},

	flashUpload: function(subcontainer) {

		if(!this.uploading) {

			this.uploading = true;

			for(var i = 0, f; f = this.filelist[i]; i++) {
				if(!f.uploading) {
					Swiff.remote(this.flashobj.toElement(), 'xFileStart', i + 1);
					this.filelist[i].uploading = true;
				}
			}

		}

	},

	flashDelete: function(fileindex) {
		this.filelist[fileindex].checked = false;
		Swiff.remote(this.flashobj.toElement(), 'xFileRemove', fileindex + 1);
	},

	flashFilter: function(filters) {
		var filtertypes = {}, assocfilters = {};
		var extensions = {
			'image': '*.jpg; *.jpeg; *.gif; *.png; *.bmp;',
			'video': '*.avi; *.mpg; *.mpeg; *.flv; *.ogv; *.webm; *.mov; *.wm;',
			'text': '*.txt; *.rtf; *.doc; *.docx; *.odt; *.sxw;',
			'*': '*.*;'
		};

		filters.split(',').each(function(val) {
			val = val.split('/').invoke('trim');
			filtertypes[val[0]] = (filtertypes[val[0]] ? filtertypes[val[0]] + ' ' : '') + '*.' + val[1] + ';';
		});

		Object.each(filtertypes, function(val, key) {
			var newindex = key == '*' ? 'All Files' : key.capitalize();
			if(val == '*.*;') val = extensions[key];
			assocfilters[newindex + ' (' + val + ')'] = val;
		});

		return assocfilters;
	},

	// appendCookieData based in Swiff.Uploader.js
	cookieData: function() {

		var hash = {};

		document.cookie.split(/;\s*/).each(function(cookie) {

			cookie = cookie.split('=');

			if(cookie.length == 2) {
				hash[decodeURIComponent(cookie[0])] = decodeURIComponent(cookie[1]);
			}
		});

		return hash;
	},

	// ------------------------- Specific methods for html5 ---------------------

	/*
	 Function: html5
	 Private method

	 Specific method for html5
	 */
	html5: function(subcontainer) {


		// Check html5 File API
		if(!window.File || !window.FileList || !window.FileReader || !window.Blob) {
			subcontainer.set('html', this.options.texts.nohtml5);
			return false;
		}

		this.baseHtml(subcontainer);

		// Trigger for html file input
		this.htmlAddFile(subcontainer);

	},

	html5Upload: function(subcontainer) {

		var filenum = this.filenum;
		this.getContainers(subcontainer).each(function(el) {
			var files = el.files;

			for(var i = 0, f; f = files[i]; i++) {
				if(typeof this.filelist[filenum] == 'undefined'){

				}
				if(this.uploadspertime <= this.options.maxuploadspertime) {

					//console.log(f.name+' = '+this.filelist[this.filenum].name);

					// Upload only checked and new files
					if(this.filelist[filenum].checked && !this.filelist[filenum].uploading) {
						this.uploading = true;
						this.filelist[filenum].uploading = true;
						this.uploadspertime++;
						this.html5send(subcontainer, this.filelist[filenum].id, f, 0, filenum, false);
					}

				}

				filenum++;

			}

		}.bind(this));

	},

	html5send: function(subcontainer, file_id, file, start, filenum, resume) {

		// Prepare request
		//var xhr = Browser.Request();


		var end = this.options.blocksize,
			action = this.options.action,
			chunk;

		var total = start + end;

		var options = this.options;

		//console.log(start+' + '+end+' = '+total);

		/*
		 if (resume)
		 action += (action.contains('?') ? '&' : '?') + 'resume=1';
		 */

		if(total > file.size)
			end = total - file.size;


		// Get slice method
		if(file.mozSlice)          // Mozilla based
			chunk = file.mozSlice(start, total)
		else if(file.webkitSlice)  // Chrome, Safari, Konqueror and webkit based
			chunk = file.webkitSlice(start, total);
		else                        // Opera and other standards browsers
			chunk = file.slice(start, total)

		var xhr = new Request({
			url: action,
			urlEncoded: false,
			noCache: true,
			headers: {
				'Cache-Control': 'no-cache',
				'X-Requested-With': 'XMLHttpRequest',
				'X-File-Name': encodeURIComponent(file.name),
				'X-File-Size': file.size,
				'X-File-Id': file_id,
				'X-File-Resume': resume,
				'Content-type': 'multipart/mixed'
			},
			onSuccess: function(response) {

				response = JSON.decode(response);

				if(this.options.listview)
					var respcontainer = document.id(subcontainer.get('id') + '_file_' + (filenum + 1));

				if(response.error == 0) {

					if(total < file.size) {
						var percent = (total / file.size) * 100;
						this.fireEvent('onFileProgress', [filenum, percent]);

						if(this.options.listview) {
							respcontainer.set('html', percent.ceil() + '%');
						}

						this.html5send(subcontainer, file_id, file, start + response.size.toInt(), filenum, true)  // Recursive upload
					}
					else {
						this.fireEvent('onFileProgress', [filenum, 100]);

						if(this.options.listview) {
							respcontainer.addClass('mooupload_noerror');
							respcontainer.set('html', this.options.texts.uploaded);
							//alert("!");

							var parent = respcontainer.getParent();
							var sel = parent.getChildren('div.optionsel');
							sel.set('html', '');
							var optionremove = new Element('a', {
								id: 'filecontrol_remove' + this.filelist.length,
								'class': 'remove'
							}); optionremove.innerHTML ="+";
							sel.grab(optionremove);

							optionremove.addEvent('click', function(e) {
								e.stop();
								if(!confirm(this.options.texts.sure)) {
									return;
								}
								this.filelist[filenum].checked = false;

								parent.getElement('div.result').set('html', this.options.texts.deleting).setStyle('background', 'url("' + this.options.url_root + '/media/mint/js/mooupload/imgs/load_bg_red.gif")').setStyle('color', 'maroon');

								var req = new Request.JSON({
									url: this.options.action_remove_file,
									method: 'post',
									autoCancel: true,
									data: {filename: response.upload_name },
									onComplete: function(json) {
										parent.slide('out');
										setTimeout(function() {
											parent.destroy();
										}, 500);
									}


								}).send();

							}.bind(this));




							if(this.options.allowEditTitle) {
								var filename_div = parent.getChildren('div.filename');
								filename_div.addClass('filenameedit');
								this.addTitleInterface(filename_div, response.row_id);
							}

							if(this.options.allowAddDescr) {
								this.addDescriptionInterface(parent, response.row_id);
							}

							/*var descrr = document.querySelector('.filedescription');
							if (document.querySelector('#addprodimg').contains(GlodObj.span)) {
							descrr.fade('show');
							}*/

							/*	var descrer = respcontainer.parentElement;
							descrer.querySelector('.filedescription').fade('show');*/

						}

						var hiddenInput = new Element('input', {
							'type': 'hidden',
							'class': 'pops',
							'name': this.options.formname,
							'value': response.upload_name
						}).inject(parent);

						this.uploadspertime--;

						this.filelist[filenum].uploaded = true;
						this.progressStep(document.id(subcontainer.get('id') + '_progresscont'));

						this.fireEvent('onFileUpload', [filenum, response]);

						if(this.uploadspertime <= this.options.maxuploadspertime)
							this.html5Upload(subcontainer);

					}
				}
				else {

					if(this.options.listview) {
						respcontainer.addClass('mooupload_error');
						respcontainer.set('html', this.options.texts.error);
						this.fireEvent('onSelectError', [response.error, response.name, response.size]);
					}

					this.uploadspertime--;

					this.filelist[filenum].uploaded = true;
					this.progressStep(document.id(subcontainer.get('id') + '_progresscont'));

					this.fireEvent('onFileUpload', [filenum, response]);

					this.fireEvent('onFileUploadError', [filenum, response]);

					if(this.uploadspertime <= this.options.maxuploadspertime)
						this.html5Upload(subcontainer);

				}

			}.bind(this)
		});


		xhr.sendBlob(chunk);


	},


















	addDescriptionInterface: function(parent, data_id, g_urojaya, v_color, v_sugar, v_sort, v_vinogrd, v_proizvod, v_orglept, v_gastronom, v_temp, v_kisl, descr_text1, vrealname, vfullpath) {

		var description_button = new Element('div', {
			'class': 'filedescr_button',
			'title': this.options.texts.edit_descr
		}).inject(parent);
          description_button.innerHTML ="<i class='mr5px fas fa-cog'></i>";
		var description = new Element('div', {
			'class': 'filedescription',
			'style': 'visibility: hidden; display: none;',
			'data-eid': data_id,
		}).inject(parent);


		description_button.addEvent('click', function(descr) {
			if(descr.getStyle('visibility') != 'hidden') {
				descr.fade('hide').hide();
				// * Fix body scroll
				document.body.classList.remove('scroll-disable');
			}
			else {
				descr.fade('show').show();
				// * Fix body scroll
				document.body.classList.add('scroll-disable');
			}
		}.pass(description));

		var text_div = new Element('div', {
			'class': 'text_div'
		}).inject(description);

		var caption = new Element('h4', {
			html: this.options.texts.edit_descr,
		}).inject(text_div);


		var text_div_opt0 = new Element('div', {'class': 'option_box',}).inject(text_div);
		var caption = new Element('h5', {'class': 'option_title', html: "Год урожая",}).inject(text_div_opt0);
		var gurojaya = new Element('textarea', {
			'class': 'textarea_g_urojaya',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt0);
		gurojaya.set('html', g_urojaya);

	    var text_div_opt = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Цвет",}).inject(text_div_opt);
		var vcolor = new Element('textarea', {
			'class': 'textarea_vcolor',
			'placeholder': 'Пример: Белое, красное и т.п...',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt);
		vcolor.set('html', v_color);


        var text_div_opt1 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Сахар",}).inject(text_div_opt1);
		var vsugar = new Element('textarea', {
			'class': 'textarea_vsugar',
			'placeholder': 'Пример: Сладкое, сухое и т.п...',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt1);
		vsugar.set('html', v_sugar);

		var text_div_opt2 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Сорт винограда",}).inject(text_div_opt2);
		var vsort = new Element('textarea', {
			'class': 'textarea_vsort',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt2);
		vsort.set('html', v_sort);

		var text_div_opt3 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Виноградник",}).inject(text_div_opt3);
		var vvinogrd = new Element('textarea', {
			'class': 'textarea_vvinogrd',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt3);
		vvinogrd.set('html', v_vinogrd);

		var text_div_opt4 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Способ производства",}).inject(text_div_opt4);
		var vproizvod = new Element('textarea', {
			'class': 'textarea_vproizvod',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt4);
		vproizvod.set('html', v_proizvod);

		var text_div_opt5 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Органолептика",}).inject(text_div_opt5);
		var vorglept = new Element('textarea', {
			'class': 'textarea_vorglept',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt5);
		vorglept.set('html', v_orglept);

		var text_div_opt6 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Гастрономия",}).inject(text_div_opt6);
		var vgastronom = new Element('textarea', {
			'class': 'textarea_vgastronom',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt6);
		vgastronom.set('html', v_gastronom);

		var text_div_opt7 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Температура подачи °C",}).inject(text_div_opt7);
		var vtemp = new Element('textarea', {
			'class': 'textarea_vtemp',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt7);
		vtemp.set('html', v_temp);

		var text_div_opt8 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Кислотность",}).inject(text_div_opt8);
		var vkisl = new Element('textarea', {
			'class': 'textarea_vkisl',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt8);
		vkisl.set('html', v_kisl);

		var text_div_opt10 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Объемная доля спирта % об.",}).inject(text_div_opt10);
		var gradus = new Element('textarea', {
			'class': 'textarea_gradus',
			'cols': 35,
			'rows': 1
		}).inject(text_div_opt10);
		gradus.set('html', gradus);

		var text_div_opt9 = new Element('div', {'class': 'option_box',}).inject(text_div);
        var caption = new Element('h5', {'class': 'option_title', html: "Дополнительно",}).inject(text_div_opt9);
		var descr_textarea = new Element('textarea', {
			'class': 'textarea_descrip',
			'cols': 35,
			'rows': 4
		}).inject(text_div_opt9);
		descr_textarea.set('html', descr_text1);

		// * Замена файла
        const formChangeFileBox = new Element('div', {
			'class': 'option_box',
		});
		text_div.prepend(formChangeFileBox);

		var editdescr = document.querySelector('div[rel="'+data_id+'"]');
		text_div.prepend(editdescr);

        new Element('h5', {
			'class': 'option_title',
			html: 'Заменить фото',
		}).inject(formChangeFileBox);

		const buttonReplaceFile = new Element('button', {
			id: `button__replace--${data_id}`,
			type: 'button',
			class: 'button button__replace',
			html: 'Выберите новый файл, для замены текущего',
		}).inject(formChangeFileBox);

		// * Разрешенные типы файлов
		const allowedTypes = (array) => {
			return `<div>Разрешенные типы файлов: ${array.join(', ')}.</div>`;
		}

		const allowedFileSize = (bytes, decimals = 2) => {
			return `<div>Максимальный размер загружаемого файла: ${this.allowedFileSize(bytes, decimals = 2)}.</div>`;
		}

		// * типы и размер поддерживаемыъ для загруки файлов
		new Element('div', {
			class: 'allowed_types',
			html: allowedTypes(this.options.exts) + allowedFileSize(this.options.maxfilesize)
		}).inject(formChangeFileBox);

		// * все MIME типы файлов
		const MIMEtypes = {
			// * изображения
			jpg: 'image/jpeg',
			png: 'image/png',
			jpeg: 'image/jpeg',
			gif: 'image/gif',
			bmp: 'image/bmp',
			webp: 'image/webp',
			avif: 'image/avif',
			svg: 'image/svg+xml',
			// * видео
			avi: 'video/x-msvideo',
			mp4: 'video/mp4',
			mpeg: 'video/mpeg',
			flv: 'video/x-flv',
			ogv: 'video/ogg',
			mov: 'video/quicktime'
			// * документы
			// ...
		};

		// * список разрешенных MIME файлов
		const thisMIMEtypes = [];
		this.options.exts.forEach(i => {
			// * без дубликатов
			if (thisMIMEtypes.indexOf(MIMEtypes[i]) === -1)
			{
				thisMIMEtypes.push(MIMEtypes[i])
			}
		});

		// * скрытый input для загрузки файла
		new Element('input', {
			id: `button__upload--${data_id}`,
			type: 'file',
			size: 1,
			accept: thisMIMEtypes.join(','),
			styles: {
				display: 'none',
			},
		}).inject(formChangeFileBox);

		// * клик по кнопке
		buttonReplaceFile.addEventListener('click', e => {
			e.preventDefault();
			this.replaceFile(data_id);
		});

/*******************************************************/

		var buttons_div = new Element('div', {
			'class': 'buttons_div joms-notifications'
		}).inject(description);

		var close_descr = new Element('div', {
			'class': 'fdescrclose'
		}).inject(buttons_div);
		close_descr.innerHTML = '<a id="exit1" class="btn-submitadd"><svg class="svg-inline--fa fa-arrow-left fa-w-14 fa-2x" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="arrow-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z"></path></svg><!-- <i class="fas fa-arrow-left fa-2x"></i> -->Отменить</a>';

		var prevu = GlodObj.fimg;

		var between = new Element('div', {
			'class': 'fimage'
		}).inject(buttons_div);

		if(typeof(prevu) != "undefined" && prevu !== null) {
			between.innerHTML = ['<img class="thumbindesc" src="', prevu, '" />'].join('');
        }

		if(typeof(vfullpath) != "undefined" && vfullpath !== null) {
			between.innerHTML = ['<img class="thumbindesc" title="', escape(vrealname), '" src="/uploads/gallery/', vfullpath, '" />'].join('');
        }


		close_descr.addEvent('click', function(descr) {
			descr.fade('hide').hide();
			// * Fix body scroll
			document.body.classList.remove('scroll-disable');
		}.pass(description));

		var save_descr = new Element('div', {
			'class': 'fdescrsave'
		}).inject(buttons_div);

		save_descr.innerHTML = '<a id="saveandexit1" class="btn-submitadd">\
				 Сохранить<svg class="svg-inline--fa fa-save fa-w-14 fa-2x" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="save" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z"></path></svg>\
				 </a>';

// Запрос к БД на получение инфы по полям файлов при нажатии редактирования описания
 var gett_descrr = function(el, opt) {
			var req1 = new Request.JSON({
				url: Cobalt.field_call_url,
				method: 'post',
				autoCancel: true,
				data: {
					field_id: opt.field_id,
					func: 'loadSaveDescr',
					record_id: opt.record_id,
					id: data_id
				},
				onComplete: function(json) {
					if(!json.success) {
						alert("Ошибка, обатитесь к администратору");
						alert(json.error);
					}
					else {
						gurojaya.set     ('html',  json.result[0].gurojaya);
						vcolor.set     ('html',  json.result[0].vcolor);
						vsugar.set     ('html',  json.result[0].vsugar);
						vsort.set      ('html',  json.result[0].vsort);
						vvinogrd.set   ('html',  json.result[0].vvinogrd);
						vproizvod.set  ('html',  json.result[0].vproizvod);
						vorglept.set   ('html',  json.result[0].vorglept);
						vgastronom.set ('html',  json.result[0].vgastronom);
						vtemp.set      ('html',  json.result[0].vtemp);
						vkisl.set      ('html',  json.result[0].vkisl);
						gradus.set     ('html',  json.result[0].gradus);
						autosize(el.querySelectorAll('textarea')); // инициализакия скрипта autosize - подключен в ../com_cobalt/views/form/default_form_wineries
					}
				}
			}).send();

		};

		description_button.addEvent('click', gett_descrr.pass([description, this.options]));

		var func_filename_descr = function(el, opt) {
			// * Fix body scroll
			document.body.classList.remove('scroll-disable');
			var req = new Request.JSON({
				url: Cobalt.field_call_url,
				method: 'post',
				autoCancel: true,
				data: {
					field_id: opt.field_id,
					func: 'onSaveDescr',
					field: 'upload',
					record_id: opt.record_id,
					id: data_id,
					text:    el.getElement('.textarea_descrip').value,
					text1:   el.getElement('.textarea_vcolor').value,
					text2:   el.getElement('.textarea_vsugar').value,
					text3:   el.getElement('.textarea_vsort').value,
					text4:   el.getElement('.textarea_vvinogrd').value,
					text5:   el.getElement('.textarea_vproizvod').value,
					text6:   el.getElement('.textarea_vorglept').value,
					text7:   el.getElement('.textarea_vgastronom').value,
					text8:   el.getElement('.textarea_vtemp').value,
					text9:   el.getElement('.textarea_vkisl').value,
					text10:  el.getElement('.textarea_gradus').value,
					text11:  el.getElement('.textarea_g_urojaya').value
				},
				onComplete: function(json) {
					if(!json.success) {
						alert(json.error);
					}
					else {
						const save_title = text_div.querySelector('.filetitlesave1.pops');
						save_title.click();
						el.fade('out');
					}
				}
			}).send();
		};
		save_descr.addEvent('click', func_filename_descr.pass([description, this.options]));

	},








	addTitleInterface: function(filename_div, data_id) {


		//var func_filename_title = function(el, opt) {

		let el = filename_div, opt = this.options;



			var input_title = new Element('input', {
				'type': 'text',
				'class': 'pops',
				'name': 'filetitle',
				'style': 'width:100%;'
			});

			var save_title = new Element('div', {
				'class': 'filetitlesave1 pops',
			});
save_title.innerHTML ="<div class='savefile1'>Сохранить<i class='mltop fas fa-save'></i></div>";


			var save_func = function(el, opt) {
				var req = new Request.JSON({
					url: Cobalt.field_call_url,
					method: 'post',
					autoCancel: true,
					data: {
						field_id: opt.field_id,
						func: 'onSaveTitle',
						field: 'upload',
						record_id: opt.record_id,
						id: data_id,
						text: input_title.value
					},
					onComplete: function(json) {
						if(!json.success) {
							alert(json.error);
						}
						else {
							input_title.set('html', json.result);
							el.set('html', json.result);
							let title = document.querySelector('span[data-fid="'+data_id+'"]')
							title.set('html', json.result);
							input_title.destroy;
							save_title.destroy;
							el.addEvent('click', function() {
								input_title.set('value', el.get('html'));
								el.set('html', '');
								el.adopt(input_title);
								el.adopt(save_title);
								el.removeEvents('click');
							});
						}
					}
				}).send();
			};

			save_title.addEvent('click', save_func.pass([el, opt]));

			input_title.removeEvents();
			input_title.addEvent('keydown', function(event) {
				if(event.key == 'enter') {
					save_func.pass([el, opt]);
				}
			});

			//input_title.set('value', el.get('html'));
			input_title.set('value', el.get('name'));
			//el.set('html', '');
			el.adopt(input_title);
			el.adopt(save_title);
			el.removeEvents('click');
		//};

		//filename_div.addEvent('click', func_filename_title.pass([filename_div, this.options]));



	},

	html5Delete: function(fileindex) {
	},

	// ------------------------- Specific methods for html4 ---------------------

	/*
	 Function: html4
	 Private method

	 Specific method for html4
	 */
	html4: function(subcontainer) {

		var subcontainer_id = subcontainer.get('id');

		// Setup some options
		this.options.multiple = false;

		var iframe = new IFrame({
			id: subcontainer_id + '_frmFile',
			name: subcontainer_id + '_frmFile',

			styles: {
				display: 'none'
			}
		});



		iframe.addEvent('load', function() {

			var response = iframe.contentWindow.document.body.innerHTML;

			if(response != '') {
				this.uploading = false;

				this.html4Upload(subcontainer);

				response = JSON.decode(response);

				if(this.options.listview)
					var respcontainer = document.id(subcontainer_id + '_file_' + (response.key + 1));

				if(response.error > 0) {
					if(this.options.listview) {
						respcontainer.addClass('mooupload_error');
						respcontainer.set('html', this.options.texts.error);
					}

					this.fireEvent('onFileUploadError', [response.key, response]);
				}
				else {

					this.filelist[response.key].uploaded = true;

					// Complete file information from server side
					this.filelist[response.key].size = response.size;

					if(this.options.listview) {
						respcontainer.addClass('mooupload_noerror');
						respcontainer.set('html', this.options.texts.uploaded);

						respcontainer.getPrevious('.filesize').set('html', response.size + ' bytes');

						var parent = respcontainer.getParent();
						var sel = parent.getChildren('div.optionsel');
						sel.set('html', '');
						var optionremove = new Element('a', {
							id: 'filecontrol_remove' + this.filelist.length,
							'class': 'remove'
						});
						sel.grab(optionremove);

						optionremove.addEvent('click', function(e) {
							e.stop();
							if(!confirm(this.options.texts.sure)) {
								return;
							}
							this.filesCount--;
							var req = new Request.JSON({
								url: this.options.action_remove_file,
								method: 'post',
								autoCancel: true,
								data: {filename: response.upload_name },
								onComplete: function(json) {
									parent.destroy();
								}
							}).send();

						}.bind(this));
					}

					var hiddenInput = new Element('input', {
						'type': 'hidden',
						'class': 'pops',
						'name': this.options.formname,
						'value': response.upload_name
					}).inject(parent);
				}

				this.progressStep(document.id(subcontainer.get('id') + '_progresscont'));

				this.fireEvent('onFileUpload', [response.key, response]);

			}

		}.bind(this)
		).inject(subcontainer);


		this.baseHtml(subcontainer);

		// Trigger for html file input
		this.htmlAddFile(subcontainer);

	},

	html4Upload: function(subcontainer) {

		// var this.filenum = 0;

		if(!this.uploading) {

			this.getForms(subcontainer).each(function(el) {

				var file = this.filelist[this.filenum];

				if(file != undefined && !this.uploading) {
					if(file.checked && !file.uploading) {
						file.uploading = true;
						this.uploading = true;
						var submit = el.submit();
					}
				}

				this.filenum++;

			}.bind(this));

		}

	},

	html4Delete: function(fileindex) {
	},

	getInputFileName: function(element) {
		var pieces = element.get('value').split(/(\\|\/)/g);

		return pieces[pieces.length - 1];
	}
















}); // end MooUpload class
