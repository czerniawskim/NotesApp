const notesCollection = document.getElementById("notes");

if (notesCollection) {
  notesCollection.addEventListener("click", e => {
    if (e.target.className === "fas fa-pen") {
      const id = e.target.getAttribute("data-id");

      var delLink = document.getElementsByClassName("fa-pen");
      for (let i = 0; i < delLink.length; i++) {
        if (delLink[i].getAttribute("data-id") == id) {
          var delPar = delLink[i].parentElement;
          var content =
            delLink[i].parentElement.parentElement.parentElement.firstChild
              .innerHTML;
          break;
        }
      }

      var editCont = document.createElement("div");
      editCont.classList.add("edit-cont");

      var editForm = document.createElement("div");
      editForm.classList.add("form-edit");

      var closeEdit = document.createElement("p");
      closeEdit.classList.add("close-edit");
      closeEdit.innerText = "X";

      editForm.appendChild(closeEdit);

      var editText = document.createElement("p");
      editText.classList.add("edit-text");
      editText.innerText = "Create note";

      editForm.appendChild(editText);

      var editContent = document.createElement("textarea");
      editContent.classList.add("edit-content");
      editContent.required = true;
      editContent.innerText = content;

      editForm.appendChild(editContent);

      var editButton = document.createElement("button");
      editButton.classList.add("edit-submit");
      editButton.innerText = "Submit";

      editForm.appendChild(editButton);

      editCont.appendChild(editForm);

      document.body.appendChild(editCont);

      closeEdit.addEventListener("click", function() {
        document.body.removeChild(editCont);
      });

      editButton.addEventListener("click", function() {
        content = editContent.value;
        document.body.removeChild(editCont);
        if (delPar) {
          delPar.innerHTML = '<i class="fas fa-spinner"></i>';
        }

        fetch("/edit/" + id + "/" + content, {
          method: "POST"
        }).then(res => window.location.reload());
      });
    }
  });
}
