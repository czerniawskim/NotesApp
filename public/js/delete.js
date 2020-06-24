const del = document.getElementsByClassName("delete");

if (del)
  Array.from(del).forEach((elem) => {
    elem.addEventListener("click", () => {
      if (confirm("Are you sure?")) {
        let id = elem.getAttribute("data-id");

        let xhr = new XMLHttpRequest();
        xhr.open("POST", `/delete/${id}`);
        xhr.onload = () => window.location.reload();
        xhr.onerror = (error) => {
          alert("Error occured. Check console");
          console.log(error);
        };
        xhr.send();
      }
    });
  });
