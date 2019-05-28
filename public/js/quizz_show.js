function selectChoice(idQuestion, idChoice) {
    $("#response_"+idQuestion).val(idChoice);
}


$(document).ready(function() {
    $("#example-basic").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slideLeft",
        enableFinishButton: false,
        autoFocus: true
    });
    $('ul[role="tablist"]').hide();
    //$('.actions > ul > li:first-child').attr('style', 'display:none');

    $("#wizard").steps({ labels: { loading: "Your custom text goes here!" } });

} );