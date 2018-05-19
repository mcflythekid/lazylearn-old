// Sizing
jQuery(document).ready(function(){
	//$("#studysession").resizable({ 
	//	handles: 'all', minHeight: 386, minWidth:588, alsoResize: '#study_flashcards' 
	//});
	adj_studysession_ht();
	jQuery(window).resize(function(){
		adj_studysession_ht();
	});
});			 
function adj_studysession_ht() {
	win_ht=jQuery(window).height();
	ssh=Math.max(win_ht-268,386);
	sfh=Math.max(win_ht-384,270);
	jQuery('#studysession').height(ssh);
	jQuery('#study_flashcards').height(sfh);
}

// Stop studying
function end_session() {
	
	$('#in').hide();
	$('#out').show();
	$('#board').hide();
	$('#study_info').hide();
	
	drawChart();
	$('#flip').hide();
	$('#mark').hide();
	if (sessionactive) {
		$('#study_stats').slideDown('slow');
	}
	sessionactive = false;
}

// Start studying
function studystart() {
	next_card();
	$('#card_front').show();
	$('#study_controls').show();
	//if (!em) {observekeys()};
	observekeys();
	google.load("visualization", "1", {packages:["piechart"]});
	update_cardset_status();
}

// Register keys
function observekeys() {
	new $(document).bind('keydown', function(event){ 
		if(event.keyCode == 40 && !edit_mode ) {mark_incorrect();event.preventDefault();} // Down
		if(event.keyCode == 38 && !edit_mode ) {mark_correct();event.preventDefault();} // Up
		if(event.keyCode == 37 && !edit_mode ) {prev_card();event.preventDefault();} // Left
		if(event.keyCode == 39 && !edit_mode ) { // Right
			if (theCardset[currcard].answered || flipped) {
				next_card();
			} else {
				flip();
			}
			event.preventDefault();
		}
	});
	
}

// Init var
var currcard = 0;
var sessionactive = true;
var flipped = false;
var edit_mode = false;
var total_num_of_cards = theCardset.length - 1;
//alert(total_num_of_cards);
//alert(theCardset.length);
var reverse_mode = false;
var total_correct = 0;
var total_incorrect = 0;
var total_unanswered = 0;

// Start
studystart();


// Report
function drawChart() {
	var pie_data = new google.visualization.DataTable();
	var min_data = Math.round(total_num_of_cards*0.02);
	var pie_options = {width: 530, height: 270, backgroundColor: '#c3d9ff', borderColor: '#c3d9ff', focusBorderColor: '#fff', legend: 'right', legendBackgroundColor: '#efefef', enableTooltip: false};
	var piec_pc = Math.round(total_correct/total_num_of_cards*1000)/10; var piei_pc = Math.round(total_incorrect/total_num_of_cards*1000)/10; var pieu_pc = Math.round(total_unanswered/total_num_of_cards*1000)/10;
	
	if (total_unanswered==0 && total_incorrect==0) {
		pie_options.colors = ['#66cc66'];
	} else if (total_correct==0 && total_incorrect==0) {
		pie_options.colors = ['#fad163'];
	} else if (total_unanswered==0 && total_correct==0) {
		pie_options.colors = ['#ff3333'];
	} else if (total_unanswered==0) {
		pie_options.colors = ['#66cc66','#ff3333'];
	} else if (total_correct==0) {
		pie_options.colors = ['#ff3333','#fad163'];
	} else if (total_incorrect==0) {
		pie_options.colors = ['#66cc66','#fad163'];
	} else {
		pie_options.colors = ['#66cc66','#ff3333','#fad163'];
	}
	
	pie_data.addColumn('string', 'Card Status');
	pie_data.addColumn('number', 'Number of Cards');
	pie_data.addRows(3);
	pie_data.setValue(0, 0, 'Correct ' + piec_pc + '%');
	pie_data.setValue(0, 1, total_correct);
	pie_data.setValue(1, 0, 'Incorrect ' + piei_pc + '%');
	pie_data.setValue(1, 1, total_incorrect);
	pie_data.setValue(2, 0, 'Unanswered ' + pieu_pc + '%');
	pie_data.setValue(2, 1, total_unanswered);
	
	if (sessionactive) {
		var pie = new google.visualization.PieChart(document.getElementById('pie_div'));
		pie.draw(pie_data, pie_options);
	}
}

// Next
function next_card() {
	if (currcard < total_num_of_cards){
		currcard++;
		display_card(false);
	}
}

// Previous
function prev_card() {
	if (currcard > 1){
		currcard--;
		display_card(false);
	}
}

//Shuffle
function shuffle() {
	var i = theCardset.length;
	if (i == 0) {return false;}
	if (edit_mode) {return false;}
	while ( --i ) {
		var j = Math.floor( Math.random() * ( i + 1 ) );
		if (j==0) {j++;}
		var tempi = theCardset[i];
		var tempj = theCardset[j];
		theCardset[i] = tempj;
		theCardset[j] = tempi;
	}
	$('#study_flashcards').effect('pulsate', {}, 60);
	currcard=1;
	display_card(false);
	if (theCardset[currcard].answered) {next_unanswered_card_or_end_session();}
}

function send_correct(){
	if (!sa) {
		$.post("/jax/markcorrect_card.php", {id : theCardset[currcard].card_id} );
	} else{
		$.post("/jax/reviewcorrect_card.php", {id : theCardset[currcard].card_id} );
	}
}

function send_incorrect(){
	$.post("/jax/markincorrect_card.php", {id : theCardset[currcard].card_id} );
}

// Right
function mark_correct() {
	if (!theCardset[currcard].answered){
		send_correct();
	} else {
		if (!theCardset[currcard].correct){
			send_correct();
		}
	}
	theCardset[currcard].answered = true;
	theCardset[currcard].correct = true;
	next_unanswered_card_or_end_session();
}

// Wrong
function mark_incorrect() {
	if (!theCardset[currcard].answered){
		send_incorrect();
	} else {
		if (theCardset[currcard].correct){
			send_incorrect();
		}
	}
	theCardset[currcard].answered = true;
	theCardset[currcard].correct = false;
	next_unanswered_card_or_end_session();
}

function display_card(with_delay) {
  flipped=false;
  show_hide_card_back();
  update_card_content();
  if (with_delay) {
    setTimeout(update_card_status, 200);
  } else {  
    update_card_status();
  }
  update_cardset_status();
}

function show_hide_card_back() {
  if (theCardset[currcard].answered) {
    $('#card_back').show();
  } else {
    $('#card_back').hide();
  }
}

function update_card_content() {
    $('#card_front').html(theCardset[currcard].card_front);
    $('#card_back').html(theCardset[currcard].card_back);
	$(document).trigger('oxford.check');
    $('#card_position').text(currcard + ' / ' + total_num_of_cards);
}

function update_card_status() {
	show_hide_card_back();
	if (theCardset[currcard].answered && theCardset[currcard].correct) {
		$('#card_status').text('CORRECT');
		$('#card_status').css('color','#66cc66');
		$('#card_status').show();
		show_marking_buttons();
    } else if(theCardset[currcard].answered) {
		$('#card_status').text('INCORRECT');
		$('#card_status').css('color','#ff3333');
		$('#card_status').show();
		show_marking_buttons();
    } else {
		$('#card_status').text('');
		$('#card_status').hide();
		show_flip_button();
    }
}

function update_cardset_status() {
  var i=0;
  total_correct=0;total_incorrect=0;total_unanswered=0;
  for (i=1;i<=total_num_of_cards;i++) { 
    if (theCardset[i].answered && theCardset[i].correct)
    {
    total_correct++;
    }
    else if (theCardset[i].answered)
    {
    total_incorrect++;
    }
    else
    {
    total_unanswered++;
    }
  }
  $('#totalc').text(total_correct);
  $('#totali').text(total_incorrect);
  $('#totalu').text(total_unanswered);
  $('#resultst').text(total_num_of_cards);
  $('#resultsc').text(total_correct);
  $('#resultsi').text(total_incorrect);
  $('#resultsu').text(total_unanswered);
  }

function flip() {
  $('#card_back').show();
  show_marking_buttons();
  flipped=true;
}

function show_flip_button() {
  $('#flip').show();
  $('#mark').hide();
}

function show_marking_buttons() {
  $('#mark').show();
  $('#flip').hide();
}



function mark_repetition(the_grade) {
  theCardset[currcard].answered = true;
  if (the_grade < 3)
    {
      theCardset[currcard].correct = false;
    } else {
      theCardset[currcard].correct = true;
    }
  //if (li && ucs && !sa) {new Ajax.Request('/results/mark_repetition?card_id=' + theCardset[currcard].card_id + '&grade=' + the_grade, {asynchronous:true, evalScripts:true});}
  if (li && ucs && !sa) {$.ajax({url: '/results/mark_repetition?card_id='+ theCardset[currcard].card_id + '&grade=' + the_grade, async: true, dataType: 'script'});}
  next_unanswered_card_or_end_session();
}

function find_next_unanswered_card() {
  next_unanswered_card=0;
  var i=1;
  do
  {
    if (!theCardset[i].answered)
      {
      next_unanswered_card=i;
      }
      i++;
  }
  while ((i<=currcard) && (next_unanswered_card==0));
  i=currcard;
  i++;
  while ((i <= total_num_of_cards) && (next_unanswered_card <= currcard))
  {
    if (!theCardset[i].answered)
      {
      next_unanswered_card=i;
      }
      i++;
  }
}

function next_unanswered_card_or_end_session() {
  update_card_status();
  update_cardset_status();
  find_next_unanswered_card();
  if (!next_unanswered_card==0)
    {
    currcard=next_unanswered_card;
    display_card(true);
    }
  else
    {
    end_session();
    }
}



function reverse() {
	reverse_mode = !reverse_mode;
	if (reverse_mode){
		$('#edit').hide();
	}else {
		$('#edit').show();
	}
	
	
	
  if (edit_mode) return false;
  for (var i=1;i<=total_num_of_cards;i++) {
    var tempf = theCardset[i].card_front;
    var tempb = theCardset[i].card_back;
    theCardset[i].card_front = tempb;
    theCardset[i].card_back = tempf;
  }
  display_card(false);
  $('#studysession').effect('highlight', {}, 500);
}

function study_edit() {
  if (edit_mode) return false;
  $.ajax({ url: '/cards/studyedit/'+ theCardset[currcard].card_id,
           beforeSend: function(xhr) {xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'))},
           async: true,
           dataType: 'script'
          });
  edit_mode = true;
}

// Update current card content
function study_update(newfront,newback) {
	theCardset[currcard].card_front = newfront;
	theCardset[currcard].card_back = newback;
	update_card_content();
	edit_mode = false;
}

function study_edit_cancel() {
  $('#study_flashcards_edit_form').remove();
  $('#study_flashcards').show();
  $('#card_status').show();
  $('#study_controls').show();
  edit_mode = false;
}

