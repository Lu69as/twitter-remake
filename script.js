window.addEventListener("load", () => {
    document.querySelector(".post_creator .characters .maxChar").innerHTML = document.querySelector(".post_creator textarea").getAttribute("maxlength");
    document.querySelector(".post_creator textarea").addEventListener("input", (evt) => {
        document.querySelector(".post_creator .characters .charUsed").innerHTML = evt.target.value.length;
    })
})