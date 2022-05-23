// this variable is the list in the dom, it's initiliazed when the document is ready
var $collectionHolder;
// the link which we click on to add new items
var $addNewItem = $('<a href="#" class="btn btn-info add_btn">Add number</a>');
// when the page is loaded and ready
$(document).ready(function () {
  $(".custom-file-input").on("change", function (event) {
    var inputFile = event.currentTarget;
    $(inputFile)
      .parent()
      .find(".custom-file-label")
      .html(inputFile.files[0].name);
  });

  $("#keyupSearch").on("keyup", function () {
    var input = $(this).val();
    var favorite = $(this).attr("data-favorite");
    var data = { input: input, favorite: favorite };
    delay(function () {
      $.ajax({
        type: "POST",
        url: "/find",
        data: data,
        dataType: "json",
        success: function (response) {
          $("#contactTable tbody").empty();
          response.forEach((element) => {
            console.log(element.favorite);
            if (element.favorite === true) {
              var favImg = "Favorite.png";
            } else {
              var favImg = "NotFavorite.png";
            }
            var row_data =
              "<tr>" +
              '<td><img src="/uploads/' +
              element.image +
              '" style="width: 40px;height: 40px;border-radius: 30px;"></td>' +
              "<td>" +
              element.fullname +
              "<br>" +
              element.email +
              "</td>" +
              "<td>" +
              '<img src="/images/' +
              favImg +
              '" class="img">' +
              "</td>" +
              "<td>" +
              '<a href="/edit/' +
              element.id +
              '">Edit</a>' +
              "</td>" +
              "<td>" +
              '<a onclick="return confirm("Are you sure to delete?")" href="/delete/' +
              element.id +
              '">Delete</a>' +
              "</td>" +
              "</tr>";
            $("#contactTable tbody").append(row_data);
          });
        },
      });
    }, 2000);
  });

  var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };
  })();

  // get the collectionHolder, initilize the var by getting the list;
  $collectionHolder = $("#phoneNumber_list");
  // append the add new item link to the collectionHolder
  $collectionHolder.append($addNewItem);

  // add an index property to the collectionHolder which helps track the count of forms we have in the list
  $collectionHolder.data("index", $collectionHolder.find(".panel").length);

  // add remove button to existing items
  $collectionHolder.find(".panel").each(function () {
    // $(this) means the current panel that we are at
    // which means we pass the panel to the addRemoveButton function
    // inside the function we create a footer and remove link and append them to the panel
    // more informations in the function inside
    addRemoveButton($(this));
  });

  // handle the click event for addNewItem
  $addNewItem.click(function (e) {
    e.preventDefault();
    // create a new form and append it to the collectionHolder
    // and by form we mean a new panel which contains the form
    addNewForm();
  });
});

/*
 * creates a new form and appends it to the collectionHolder
 */
function addNewForm() {
  // getting the prototype
  // the prototype is the form itself, plain html
  var prototype = $collectionHolder.data("prototype");
  // get the index
  // this is the index we set when the document was ready, look above for more info
  var index = $collectionHolder.data("index");
  // create the form
  var newForm = prototype;
  // replace the __name__ string in the html using a regular expression with the index value
  console.log(newForm);
  newForm = newForm.replace(/__name__/g, index);
  console.log(newForm);
  // incrementing the index data and setting it again to the collectionHolder
  $collectionHolder.data("index", index + 1);
  // create the panel
  // this is the panel that will be appending to the collectionHolder
  var $panel = $('<div class="panel panel-warning form_row"></div>');
  // create the panel-body and append the form to it
  var $panelBody = $('<fieldset class="form-group"></fieldset>').append(
    newForm
  );
  // append the body to the panel
  $panel.append($panelBody);
  // append the removebutton to the new panel
  addRemoveButton($panel);
  // append the panel to the addNewItem
  // we are doing it this way to that the link is always at the bottom of the collectionHolder
  $addNewItem.before($panel);
}

/**
 * adds a remove button to the panel that is passed in the parameter
 * @param $panel
 */
function addRemoveButton($panel) {
  // create remove button
  var $removeButton = $('<a href="#" class="btn btn-danger">Remove</a>');
  // appending the removebutton to the panel footer
  var $panelFooter = $('<div class="panel-footer"></div>').append(
    $removeButton
  );
  // handle the click event of the remove button
  $removeButton.click(function (e) {
    e.preventDefault();
    // gets the parent of the button that we clicked on "the panel" and animates it
    // after the animation is done the element (the panel) is removed from the html
    $(e.target)
      .parents(".panel")
      .slideUp(1000, function () {
        $(this).remove();
      });
  });
  // append the footer to the panel
  $panel.append($panelFooter);
}
