// изолируем события и ждем загрузки html
document.addEventListener('DOMContentLoaded', () => {

    if(document.querySelector('.cat-list-card') == null){
        document.querySelector('.text-more').onclick = function(){
            if(document.querySelector('.text-more').innerText == 'Подробнее'){
                document.querySelector('.text-ellipsis-mobile').style.display = 'none';
                document.querySelector('.text-next').style.display = 'contents';
                document.querySelector('.text-more').innerText = 'Свернуть';    
            }else{
                document.querySelector('.text-ellipsis-mobile').style.display = '';
                document.querySelector('.text-next').style.display = 'none';
                document.querySelector('.text-more').innerText = 'Подробнее';
            }
        }
    }else{
        let a = document.querySelectorAll('.text-more');
        for(let i in a){
            a[i].style.display = 'none';
        }
    }

    // * переменные
    // каталог карточек
    const catalog = document?.querySelector('.catalog');

    // если каталок существует
    if (catalog) {
        // контейнер карточек
        const catalogContainer = document.querySelector('.catalog__container');
        // кнопка переключения вида каталога
        const catalogSwitcher = document.querySelector('.catalog__switcher');
        // кол-во столбцов в сетке, по умолчанию 2, см. файл scss/components/_catalog.scss
        const catalogGridCount = 2;

        // * функции
        // функция переключения вида каталога
        const catalogSwitch = () => {
            // если вид каталога указан как сетка
            if (catalog.classList.contains('catalog--grid'))
            {
                // переключаем вид на список
                catalog.classList.remove('catalog--grid');
                catalog.classList.add('catalog--list');

                // сохраняем вид в локальное хранилище
                catalogSaveView('list')
            }
            // если вид каталога указан как список
            else if (catalog.classList.contains('catalog--list'))
            {
                // переключаем вид на список
                catalog.classList.remove('catalog--list');
                catalog.classList.add('catalog--grid');

                // сохраняем вид в локальное хранилище
                catalogSaveView('grid')
            }
        }

        // функция сохранения вида каталога в локальное хранилище клиента
        const catalogSaveView = (view) => {
            localStorage.setItem('catalog-view', view);
        }

        // функция получения вида каталога из локального хранилища клиента
        const catalogGetView = () => {
            return localStorage.getItem('catalog-view');
        }

        // функция перестройки последнего элемента, если есть дырка в сетке
        const catalogLastCardFix = () => {
            // считаем пропорции, если есть пробитие, т.е. дырка, добавляем последней картчоке модификатор, что бы она заполнита своим телом дыру =)
            if (catalogContainer.childElementCount % catalogGridCount > 0 && !catalogContainer.lastElementChild.classList.contains('card--full'))
            {
                catalogContainer.lastElementChild.classList.add('card--full');
            }

            // упростил логику, это не понадобилось
            // если окно больше 960px, т.е. это ПК
            // значение matchMedia берем из scss/components/_catalog.scss
            // if (window.matchMedia("(min-width: 960px)").matches)
            // {
            // }
        }

        // * различные стартовые условия, которые грузятся в месте с html
        // если в локальном хранилищеклиента сохранен вид каталога, добавляем каталогу модификатор
        if (catalogGetView() !== null)
        {
            catalog.classList.add('catalog--' + catalogGetView());
        }
        // иначе указываем вид по умолчанию - список
        else
        {
            catalog.classList.add('catalog--list');
            catalogSaveView('list');
        }

        // фиксим последний элемент
        catalogLastCardFix();

        // * события
        // переключаем вид каталога
        catalogSwitcher.addEventListener('click', e => {
            catalogSwitch()
        });

        // подгонка последней ячейки под нужный размер, что бы не было пустой дырки
        // TODO: добавить задержку (throttling или debouncing) на это событие
        window.addEventListener('resize', e => {
            catalogLastCardFix();
        }, true);
    }
    (function(){
        let speed = 2; // Скорость скролла.

        let scroll = document.querySelector('.product-wrapper');

        let left = 0; // отпустили мышку - сохраняем положение скролла
        let drag = false;
        let coorX = 0; // нажали мышку - сохраняем координаты.

        scroll.addEventListener('mousedown', function(e) {
          drag = true;
          coorX = e.pageX - this.offsetLeft;
        });
        document.addEventListener('mouseup', function() {
          drag = false;
          left = scroll.scrollLeft;
        });
        scroll.addEventListener('mousemove', function(e) {
          if (drag) {
            this.scrollLeft = left + (e.pageX - this.offsetLeft - coorX)*speed;
          }
        });

        })();


        $( "" ).click(function() {
            $('.text-next').css('display', 'block');
            $('.text-more').css('display', 'none');
          });


})

