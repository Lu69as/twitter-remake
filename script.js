window.addEventListener("load", () => {
    // document.querySelector(".post_creator .characters .maxChar").innerHTML = document.querySelector(".post_creator textarea").getAttribute("maxlength");
    // document.querySelector(".post_creator textarea").addEventListener("input", (evt) => {
    //     document.querySelector(".post_creator .characters .charUsed").innerHTML = evt.target.value.length;
    // })

    document.querySelectorAll("form.sign_up input").forEach((e) => {
        e.addEventListener("input", () => {
            document.querySelectorAll("form.sign_up:not(:has(input[required]:invalid)) button").forEach((b) => b.classList.remove("invalid"));
            document.querySelector(".login_select .userId_list").innerHTML.split("|").forEach((u) => {
                if (u == document.querySelector("form.sign_up #userId").value) 
                    document.querySelector("form.sign_up button").classList.add("invalid");
            });
        })
    })
})