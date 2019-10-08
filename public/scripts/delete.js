const notes = document.getElementById("notes");

if (notes) {
  notes.addEventListener("click", e => {
    if (e.target.className === "fas fa-trash-alt") {
      if (confirm("Are you sure?")) {
        const id = e.target.getAttribute("data-id");

        var delLink = document.getElementsByClassName("fa-trash-alt");
        for (let i = 0; i < delLink.length; i++) {
          if (delLink[i].getAttribute("data-id") == id) {
            var delPar = delLink[i].parentElement;
            break;
          }
        }
        if (delPar) {
          delPar.innerHTML = '<i class="fas fa-spinner"></i>';
        }

        fetch("/delete/" + id, {
          method: "POST"
        }).then(res => window.location.reload());
      }
    }
  });
}
