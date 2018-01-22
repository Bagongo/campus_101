import $ from "jquery";

class Like{

    constructor(){
        this.events();
    }

    events()
    {
        $(".like-box").on("click", this.clickDispatcher.bind(this));
    }

    createLike(currentLikeBox)
    {
        var professorID = currentLikeBox.data("professor");
        $.ajax({
            url: universityData.root_url + "/wp-json/university/v1/manageLike/",
            type: "POST",
            data: {"professorID": professorID},
            success: (response) => console.log(response),
            error: (response) => console.log(response)
        });
    }

    deleteLike(currentLikeBox)
    {
        $.ajax({
            url: universityData.root_url + "/wp-json/university/v1/manageLike/" ,
            type: "DELETE",
            success: (response) => console.log(response),
            error: (response) => console.log(response)
        });
    }

    clickDispatcher(e)
    {
        var currentLikeBox = $(e.target).closest('.like-box');

        if(currentLikeBox.data("exists") == "yes")
            this.deleteLike(currentLikeBox);
        else
            this.createLike(currentLikeBox);
    }

}

export default Like;