var datasets = [];

function load_datasets() {
    $.getJSON("datasets/animals.json", function(json) {
        datasets.animals = json;
    });
}