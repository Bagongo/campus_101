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
        console.log("delete-note...");
    }

}

export default MyNotes;