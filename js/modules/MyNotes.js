import $ from 'jquery';

class MyNotes {


    constructor() {
        this.events();
    }

    events()
    {
        $(".delete-note").on("click", this.deleteNote.bind(this));
        $(".edit-note").on("click", this.editNote.bind(this));
        $(".update-note").on("click", this.updateNote.bind(this));
    }

    deleteNote(e)
    {
        var thisNote = $(e.target).parents("li");

        $.ajax({
            beforeSend: (xhr)=>{
                xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
            },
            url: universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"),
            type: "DELETE",
            success: (response) => {
                console.log("delete callback!!!!");
                console.log(response);
                thisNote.slideUp();
            },
            error: (response) => {
                console.log("failed delete.....");
                console.log(response);
            }
        });
    }

    updateNote(e)
    {
        var thisNote = $(e.target).parents("li");
        var updatedNote = {
            title: thisNote.find(".note-title-field").val(),
            content: thisNote.find(".note-body-field").val()
        };

        $.ajax({
            beforeSend: (xhr)=>{
                xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
            },
            url: universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"),
            type: "POST",
            data: updatedNote,
            success: (response) => {
                this.saveDataToNote(thisNote, updatedNote);
                this.makeNoteReadonly(thisNote);
                console.log("delete callback!!!!");
                console.log(response);
            },
            error: (response) => {
                console.log("failed update.....");
                console.log(response);
            }
        });
    }

    saveDataToNote(note, data)
    {
        note.attr("data-title", data.title);
        note.attr("data-body", data.content);
    }

    makeNoteEditable(note)
    {
        var title = note.find(".note-title-field");
        var body = note.find(".note-body-field");

        this.saveDataToNote(note, {
            title: title.val(),
            content: body.val()
        });
     
        note.find(".edit-note").html("<i class='fa fa-times' aria-hidden='true'></i> Cancel");
        $([title, body]).each(function(){
            $(this).removeAttr("readonly").addClass("note-active-field");
        });
        note.find(".update-note").addClass("update-note--visible");
        note.data("state", "editable");
    }

    makeNoteReadonly(note)
    {
        var title = note.find(".note-title-field");
        var body = note.find(".note-body-field");

        title.val(note.data("title"));
        body.val(note.data("body"));
        note.find(".edit-note").html("<i class='fa fa-pencil' aria-hidden='true'></i> Edit");
        $([title, body]).each(function(){
            $(this).attr("readonly", "readonly").removeClass("note-active-field");
        });
        note.find(".update-note").removeClass("update-note--visible");
        note.data("state", "uneditable");
    }

    editNote(e)
    {
        var thisNote = $(e.target).parents("li");

        if(thisNote.data("state") == "editable")
        {
            this.makeNoteReadonly(thisNote);
        }
        else
        {
            this.makeNoteEditable(thisNote);
        }
    }

}

export default MyNotes;