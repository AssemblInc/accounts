function showLoading(event) {
    console.log("onbeforeunload fired!");

    var loading = document.getElementById("loading");
    loading.style.display = "block";
}

window.onbeforeunload = showLoading;