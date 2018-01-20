import $ from 'jquery';

class MyNotes {


    constructor() {
        this.events();
    }

    events()
    {
        // parent selector - event - children selector expression (for event to be listened on notes created after page load)....
        $("#my-notes").on("click", ".delete-note", this.deleteNote.bind(this));
        $("#my-notes").on("click", ".edit-note", this.editNote.bind(this));
        $("#my-notes").on("click", ".update-note", this.updateNote.bind(this));
        $(".submit-note").on("click", this.createNote.bind(this));
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

                if(response.userNoteCount <= 4)
                    $(".note-limit-message").removeClass("active");
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
                console.log("update note callback!!!!");
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

    createNote()
    {
        var newNote = {
            title: $(".new-note-title").val(),
            content: $(".new-note-body").val(),
            status: "publish"
        };

        $.ajax({
            beforeSend: (xhr)=>{
                xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
            },
            url: universityData.root_url + "/wp-json/wp/v2/note/",
            type: "POST",
            data: newNote,
            success: (response) => {
                $(".new-note-title, .new-note-body").val("");
                $(`
                    <li data-id="${response.id}">
                        <input readonly class="note-title-field" value="${response.title.raw}">
                        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
                        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>
                        <textarea readonly class="note-body-field">
                            ${response.content.raw}
                        </textarea>
                        <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                    </li>    
                `).prependTo("#my-notes").hide().slideDown();
                console.log("create callback!!!!");
                console.log(response);
            },
            error: (response) => {
                if(response.responeMessage = "You have reached note limit!")
                    $(".note-limit-message").addClass("active");

                console.log("failed creating note.....");
                console.log(response);
            }
        });
    }


}

export default MyNotes;