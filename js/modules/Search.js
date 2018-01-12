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
        $.getJSON(universityData.root_url + "/wp-json/university/v1/search?term=" + this.searchField.val(), (results) => {
            console.log(results);
            this.resultField.html(`
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information</h2>
                        ${results.generalInfo.length ? `<ul>` : `<p>No general information matches your search</p>`}
                            ${results.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.postType == "post" ? `by ${item.authorName}` : ''}</li>`).join("")}
                        ${results.generalInfo.length ? `</ul>` : ""}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Program(s)</h2>
                        ${results.programs.length ? `<ul>` : `<p>No programs matches your search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}
                            ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join("")}
                        ${results.programs.length ? `</ul>` : ""}
                        <h2 class="search-overlay__section-title">Professor(s)</h2>
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Campus(es)</h2>
                        ${results.campuses.length ? `<ul>` : `<p>No campuses matches your search. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
                            ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join("")}
                        ${results.campuses.length ? `</ul>` : ""}
                        <h2 class="search-overlay__section-title">Event(s)</h2>
                    </div>
                </div>
            `);

            this.isSpinnerVisible = false;
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