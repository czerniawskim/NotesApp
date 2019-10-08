const addButton = document.getElementsByClassName("add")[0];

if (addButton) {
  addButton.addEventListener("click", function() {
    var addCont = document.createElement("div");
    addCont.classList.add("add-cont");

    var addForm = document.createElement("div");
    addForm.classList.add("form-add");

    var closeAdd = document.createElement("p");
    closeAdd.classList.add("close-add");
    closeAdd.innerText = "X";

    addForm.appendChild(closeAdd);

    var addText = document.createElement("p");
    addText.classList.add("add-text");
    addText.innerText = "Create note";

    addForm.appendChild(addText);

    var addContent = document.createElement("textarea");
    addContent.classList.add("add-content");
    addContent.required = true;

    addForm.appendChild(addContent);

    var addButton = document.createElement("button");
    addButton.classList.add("add-submit");
    addButton.innerText = "Submit";

    addForm.appendChild(addButton);

    addCont.appendChild(addForm);

    document.body.appendChild(addCont);

    closeAdd.addEventListener("click", function() {
      document.body.removeChild(addCont);
    });

    addButton.addEventListener("click", function() {
      addButton.innerHTML = "<i class='fas fa-spinner'></i>";
      addContent.readOnly = true;
      addButton.classList.add("disabled");

      if (addContent !== null) {
        fetch("/new/" + addContent.value, {
          method: "POST"
        }).then(res => window.location.reload());
      }
    });
  });
}
