import $ from 'jquery';

class MyNotes {


    constructor() {
        this.events();
    }

    events()
    {
        $(".delete-note").on("click", this.deleteNote.bind(this));
    }

    deleteNote()
    {
        $.ajax({
            beforeSend: (xhr)=>{
                xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
            },
            url: universityData.root_url + "/wp-json/wp/v2/note/" + "117",
            type: "DELETE",
            success: (response) => {
                console.log("delete callback!!!!");
                console.log(response);
            },
            error: (response) => {
                console.log("failed delete.....");
                console.log(response);
            }
        });
    }

}

export default MyNotes;