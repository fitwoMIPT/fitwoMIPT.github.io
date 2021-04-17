(function($){
  var $btns = $('.gc-admin__tabs a').click(function() {
    var $el = $('.' + this.id).fadeIn(450);
    $('.gc-admin__tab').not($el).hide();

    $btns.removeClass('active');
    $(this).addClass('active');
  })

  var dropdownToggle = ('.gc-dropdown__toggle');
  var dropdownBox = ('.gc-dropdown__box');
  var dropdownIcons = ('.gc-dropdown__icons');
  var dropdownIcon = ('.gc-dropdown__icons > a');
  var dropdownSelect = ('.gc-dropdown__icons-select');
  var currentValue = $(dropdownSelect).val();
  var currentIcon = $(dropdownIcons).find('#' + currentValue).attr('class');

  $(dropdownToggle).children('a').click(function() {
    $(this).parent().siblings(dropdownBox).fadeToggle();
  });

  $(document).click(function (e) {
    e.stopPropagation();
    var container = $(".gc-dropdown");

    //check if the clicked area is dropDown or not
    if (container.has(e.target).length === 0) {
        $(dropdownBox).hide();
    }
  })

  $(dropdownToggle).children('a').removeClass().addClass(currentIcon);

  $(dropdownIcon).click(function() {
    var newClass = $(this).attr('class');
    var parent = $(this).parent().parent().siblings(dropdownToggle);
    var iconID = $(this).attr('id');

    $(parent).children('a').removeClass().addClass(newClass);
    $(dropdownSelect).val(iconID);
    $(dropdownBox).hide();
  });

})(jQuery)
