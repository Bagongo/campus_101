import $ from "jquery";

class Like{

    constructor(){
        this.events();
    }

    events()
    {
        $(".like-box").on("click", this.clickDispatcher.bind(this));
    }

    createLike()
    {
        console.log("create like!!!!");
    }

    deleteLike()
    {
        console.log("delete like!!!!!");
    }

    clickDispatcher(e)
    {
        var currentLikeBox = $(e.target).closest('.like-box');

        if(currentLikeBox.data("exists") == "yes")
            this.deleteLike();
        else
            this.createLike();
    }

}

export default Like;