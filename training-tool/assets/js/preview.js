// Uncomment for fake i18n
// fakei18n = function (obj) {
//   if (typeof obj === 'string') {
//     return Array(obj.length + 1).join('_');
//   } else {
//     for (i in obj) {
//       obj[i] = fakei18n(obj[i]);
//     }
//
//     return obj;
//   }
// }
//
// for (i in FormRenderer.t) {
//   FormRenderer.t[i] = fakei18n(FormRenderer.t[i])
// }

$('.js_stored_val').each(function(){
  var stored = store.get($(this).attr('id'));

  if (stored) {
    $(this).val(stored);
  } else {
    $(this).val($(this).find('option').first().val());
  }
});

$('.js_stored_val').change(function(){
  store.set($(this).attr('id'), $(this).val());
  location.reload();
});

// Load libraries
$('head').
  append($('<link rel="stylesheet" type="text/css" />').attr('href', $('#lib').val()));

// Just append every possible class, it's easiest for now
if ($('#lib').val().match('cardinal')) {
  FormRenderer.BUTTON_CLASS = 'button button-primary'
} else if ($('#lib').val().match('bootstrap')) {
  FormRenderer.BUTTON_CLASS = 'btn btn-primary'
} else if ($('#lib').val().match('foundation')) {
  FormRenderer.BUTTON_CLASS = 'button button-primary'
}

// Initialize form
if ($('#screendoor_project_id').val()) {
  var fr = new FormRenderer({
      screendoorBase: 'http://screendoor.dobt.dev',
      project_id: $('#screendoor_project_id').val()
  });
} else {
  var fr = new FormRenderer($.extend(
    Fixtures.FormRendererOptions[$('#fixture').val()](),
    {
      screendoorBase: 'http://screendoor.dobt.dev',
      onReady: function(){
        console.log('Form is ready!');
      },
      plugins: ['WarnBeforeUnload', 'BottomBar', 'ErrorBar', 'LocalStorage']      
    }
  ));

  fr.save = function(options){
    this.state.set({
      hasChanges: false
    });   
    
    if($(".frontpage").length > 0){        
        if($("#survey_sumitted").length > 0 && $("#survey_sumitted").val() == 1){
            return false;
        }
        var survey_id = $("#survey_id").val();
        savesurveydata(survey_id,this.getValue());
    }        
    
  };
  
}
