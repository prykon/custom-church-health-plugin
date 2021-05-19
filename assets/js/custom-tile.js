function distributeFields() {
    var radius = 100;
    var fields = $('.custom-church-health-item'), container = $('#custom-church-health-items-container'),
        width = container.width(),
        height = container.height(),
        angle = 0,
        step = (2*Math.PI) / fields.length;
        y_offset = -35;
    fields.each(function() {
        var x = Math.round(width/2 + radius * Math.cos(angle) - $(this).width()/2);
        var y = Math.round(height/2 + radius * Math.sin(angle) - $(this).height()/2) + y_offset;
        
        $(this).css({
            left: x + 'px',
            top: y + 'px'
        });
        angle += step;
    });
}

distributeFields();