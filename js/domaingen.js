// const ox = require('oxford_3000');
// console.log( ox.length );

// @namecheap css hack
// $( '.taken' ).parent().parent().css("border", "1px solid red").remove();
// var datasets = [];


// A $( document ).ready() block.
$( document ).ready(function() {
    var primary = $('#primary');
    var secondary = $('#secondary');
    var trinary = $('#trinary');

    console.log( primary.val() );

    primary.change(function() {
        updateResults();
    });

    secondary.change(function() {
        updateResults();
    });

    secondary.change(function() {
        updateResults();
    });

    function updateResults() {
        $( '#results' ).text('');
        console.log( datasets['adwords_cpc'] );

    }

}, jQuery );

// function load_datasets() {
//     $.getJSON("datasets/animals.json", function(json) {
//         datasets.animals = json;
//     });
// }