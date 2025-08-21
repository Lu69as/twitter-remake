function checkForm(f) {
    if (f.checkValidity()) {
        f.querySelector(`button`).classList.remove("invalid");
        document.querySelector(`.login_select .userId_list`).innerHTML.split("|").forEach((u) => {
            if (u == document.querySelector(`form.sign_up .userId`).value)
                document.querySelector(`form.sign_up button`).classList.add("invalid");
        });
    };
};

window.addEventListener("load", () => {
    document.querySelectorAll(".post_creator textarea").forEach((e) => { 
        document.querySelector(".post_creator .characters .maxChar").innerHTML = e.getAttribute("maxlength");
        e.addEventListener("input", () => document.querySelector(".post_creator .characters .charUsed").innerHTML = e.value.length );
    });
    
    document.querySelectorAll(".post_creator .row2 .blobs").forEach((e) => e.addEventListener("keydown", (evt) => {
        if (evt.key == "Enter") {
            evt.preventDefault();
            let inputBlobs = document.querySelector(".post_creator .blobs_selected input");
            inputBlobs.value += (inputBlobs.value.length > 0 ? "|§" : "§") + e.value + "§";

            let blob = document.createElement("p");
            blob.addEventListener("click", () => {
                blob.remove();
                inputBlobs.value = inputBlobs.value.replace(`§${blob.innerText}§`, "")
                    .replace("||", "|").replace(/\|$/, '').replace(/^\|/, '');
            })
            blob.innerHTML = e.value;
            document.querySelector(".post_creator .blobs_selected").append(blob);
            e.value = "";
        }
        const regex = /[a-zA-Z0-9]/;
        if (!regex.test(evt.key) && evt.key !== 'Backspace' && evt.key !== ' ') evt.preventDefault();
    }))

    document.querySelectorAll("form:is(.sign_up, .log_in)").forEach((e) => {
        checkForm(e);
        e.querySelectorAll("input").forEach((i) => i.addEventListener("input", () => checkForm(e)));
        e.addEventListener("submit", (s) => {
            if (e.querySelector(`button`).classList.contains("invalid")) s.preventDefault(); 
        });
    });

    document.querySelectorAll('.sorting [data-sort]').forEach((e) => {
        e.addEventListener('click', () => {
            const params = new URLSearchParams(window.location.search);
            params.set('sort', e.dataset.sort);
            window.location.search = params.toString();
        });
    });

    document.querySelectorAll('.sorting .orderBy').forEach((e) => { e.addEventListener('click', () => {
            const params = new URLSearchParams(window.location.search);
            params.set('order', e.classList[1] == "ASC" ? "DESC" : "ASC");
            window.location.search = params.toString();
        });
    });

    document.querySelectorAll(".login_tabs > div").forEach((e) => { e.addEventListener("click", () => {
        let s = document.querySelector(".login_tabs div:not(."+ e.classList[0] +")");
        e.style.opacity = "1"; s.style.opacity = ".7";
        document.querySelector("form."+ s.classList[0]).style.display = "none";
        document.querySelector("form."+ e.classList[0]).style.display = "block";
    })});

    document.querySelectorAll(".btn1.delete").forEach((e) => { e.addEventListener("click", (evt) => {
        if (!confirm("You are about to delete your Blob account \nAre you sure you want to leave us?"))
            evt.preventDefault();
    })});
})