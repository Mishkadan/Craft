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