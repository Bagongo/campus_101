import $ from "jquery";

class Search
{
    constructor(){
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term");
        this.resultField = $("#search-overlay__result");
        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;
        this.previousSearch;
        this.events();
        this.typingTimer; 
    }

    events()
    {
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this));
        this.searchField.on("keyup", this.typingLogic.bind(this));
    }

    keyPressDispatcher(e)
    {
        if(e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(":focus"))
            this.openOverlay();

        if(e.keyCode == 27 && this.isOverlayOpen)
            this.closeOverlay();
    }

    openOverlay()
    {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.isOverlayOpen = true;
    }

    closeOverlay()
    {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false;
    }

    typingLogic(e)
    {
        if(this.searchField.val() != this.previousSearch)
        {
            clearTimeout(this.typingTimer);

            if(this.searchField.val())
            {
                if(!this.isSpinnerVisible)
                {
                    this.resultField.html("<div class='spinner-loader'></div>");
                    this.isSpinnerVisible = true;
                }
                
                this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
            }
            else
            {
                this.resultField.html(" ");
                this.isSpinnerVisible = false;
            }
        }

        this.previousSearch = this.searchField.val();        
    }

    getResults()
    {
        $.getJSON("http://localhost:3000/wp-json/wp/v2/posts?search=" + this.searchField.val(), data => {
            this.resultField.html(`
                    <h2 class="search-overlay__section-title">General Information</h2>
                    <ul class="link-list min-list">
                        ${data.map(item => `<li><a href="${item.link}">${item.title.rendered}</a></li>`).join("")}
                    </ul>
                `);
        })
    }

}

export default Search;