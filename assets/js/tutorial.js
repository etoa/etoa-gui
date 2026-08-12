
// Minimize tutorial
const minimizeTutorial = function () {
  $('.tutorialBox').hide();
  $('.tutorialBoxReduced').show();
  $.cookie('tutorial_minimize', 'yes');
}

// Restore tutorial
const restoreTutorial = function () {
  $('.tutorialBox').show();
  $('.tutorialBoxReduced').hide();
  $.cookie('tutorial_minimize', 'no');
}

const openTutorial = function () {
  $('#tutorialContainer').show();
  $('.tutorialBoxReduced').hide();

  if ($.cookie('tutorial_minimize') == 'yes') {
    minimizeTutorial();
  }
}

const closeTutorial = function () {
  $('#tutorialContainer').hide();
  $.ajax({
    type: 'PUT',
    url: '/api/tutorials/' + $('#tutorialContainer').attr('data-tutorial') + '/close',
    contentType: 'application/json'
  }).fail(alert);
}

function showTutorialText(id, step) {
  ajaxRequest('/api/tutorials/' + id, { step: step }, function (data) {
    if (data.title && data.content) {
      $('#tutorialContainer').attr('data-tutorial', id);
      $('.tutorialTitleContent').html(data.title);
      $('.tutorialContent').html(data.content);
      if (data.prev !== null) {
        $('.tutorialPrev').show();
        $('.tutorialPrev').unbind("click");
        $('.tutorialPrev').click(function () {
          showTutorialText(id, data.prev);
        });
      } else {
        $('.tutorialPrev').hide();
      }
      if (data.next !== null) {
        $('.tutorialNext').show();
        $('.tutorialNext').unbind("click");
        $('.tutorialNext').click(function () {
          showTutorialText(id, data.next);
        });
        $('.tutorialFinish').hide();
      } else {
        $('.tutorialNext').hide();
        $('.tutorialFinish').show();
      }
    }
  }, alert);
  openTutorial();
}

$(function () {
  $('.tutorialMinimize').click(minimizeTutorial);
  $('.tutorialRestore').click(restoreTutorial);
  $('.tutorialClose').click(function () {
    if (confirm('Tutorial wirklich schliessen?')) {
      closeTutorial()
    }
  });
  $('.tutorialFinish').click(closeTutorial);
});

// Used by the "Anzeigen" button in the game settings. The tutorial was closed, so the
// closed flag has to be reset server-side before it shows up on the other pages again.
function reopenTutorial (id, step) {
  $.ajax({
    type: 'PUT',
    url: '/api/tutorials/' + id + '/reopen',
    contentType: 'application/json'
  }).done(function () {
    showTutorialText(id, step)
  }).fail(alert)
}

window.showTutorialText = showTutorialText
window.reopenTutorial = reopenTutorial
