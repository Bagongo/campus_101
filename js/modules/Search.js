import $ from "jquery";

class Search
{
    constructor(){
        this.addSearchHTML();
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
        this.searchField.val("");
        setTimeout(() => this.searchField.focus(), 1000);
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
                
                this.typingTimer = setTimeout(this.getResults.bind(this), 750);
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
        $.when(
            $.getJSON(universityData.root_url + "/wp-json/wp/v2/posts?search=" + this.searchField.val()),
            $.getJSON(universityData.root_url + "/wp-json/wp/v2/pages?search=" + this.searchField.val())
        ).then((posts, pages) => {
            var combinedResults = posts[0].concat(pages[0]);
            // console.table(combinedResults);
            this.resultField.html(`
                    <h2 class="search-overlay__section-title">General Information</h2>
                    ${combinedResults.length ? `<ul>` : `<p>No general information matches your search</p>`}
                        ${combinedResults.map(item => `<li><a href="${item.link}">${item.title.rendered}</a></li>`).join("")}
                    ${combinedResults.length ? `</ul>` : ""}
                `);

            this.isSpinnerVisible = false;
        }, () => {
            this.resultField.html("There was an error with the search");
        });
    }

    addSearchHTML()
    {
        $("body").append(`
            <div class="search-overlay">
              <div class="search-overlay__top">
                <div class="container">
                  <i class="fa fa-search fa-3x search-overlay__icon" aria-hidden="true"></i>
                  <input type="text" class="search-term" placeholder="What are you looking for...?" id="search-term">
                  <i class="fa fa-window-close fa-3x search-overlay__close" aria-hidden="true"></i>
                </div>
              </div>

              <div class="container">
                <div id="search-overlay__result"></div>
              </div>
            </div>
        `);
    }

}

export default Search;