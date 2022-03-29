let guide = [];
let programs = [];

const weekDays = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

function load_audio(url, title, image = null) {
  if (!window.pageComponent) {
    alert("The audio player has not been loaded correctly");
  }
  window.pageComponent.loadTrack({
    title: title,
    url: url,
    image: image,
  });
}

(function ($) {
  "use strict";
})(jQuery);
console.log("Station", station_vars);
jQuery(".listen-live-button").on("click", async function (e) {
  newwindow = window.open(
    `${window.location.origin}/player`,
    "name",
    `height=700,width=700`
  );
  if (window.focus) {
    newwindow.focus();
  }
  return false;
});

const pad = (d) => {
  return d < 10 ? "0" + d.toString() : d.toString();
};
let guides = document.getElementsByClassName("programguide");
let onairs = document.getElementsByClassName("onairnow");
let list = document.getElementsByClassName("program-list");
let programDiv = jQuery("#mrepisodes");

const sortOutOffset = (thisSlot) => {
  let newSlot = { ...thisSlot };
  let weekday = thisSlot.weekday_start;
  let start = thisSlot.hour_start;
  start = start - parseInt(station_vars.offset);
  if (start > 23) {
    start = start - 24;
    weekday++;
  }
  if (start < 0) {
    start = start + 24;
    weekday--;
  }
  if (weekday > 6) {
    weekday = 0;
  }
  if (weekday < 0) {
    weekday = 6;
  }

  return { ...thisSlot, hour_start: start, weekday_start: weekday };
};

const getOnAir = async () => {
  const response = await fetch(
    `https://app.myradio.click/api/public/station/${station_vars.api_key}/onair`
  );
  const results = await response.json();

  console.log("on air", results);

  for (let index = 0; index < onairs.length; index++) {
    const element = onairs[index];
    let newTitle = document.createElement("h3");
    let img = document.createElement("img");
    newTitle.append(`ONAIR: ${results.data.slot.program.name}`);
    if (results.data.slot.program.image) {
      img.src = results.data.slot.program.image.url;
      element.append(img);
    }
    element.append(newTitle);
    element.classList.remove("loading");
  }
};

const getProgramEpisodes = async (slug) => {
  const response = await fetch(
    `https://app.myradio.click/api/public/station/${station_vars.api_key}/program/${slug}?showEpisodes=true`
  );
  const div = jQuery("#mrepisodes");
  const results = await response.json();

  if (results?.data?.episodes) {
    div.append("<h4 id='LatestEpisodes'>Latest Episodes</h4>");
    results.data.episodes.map((item, i) => {
      console.log("station", station_vars);
      // let url = `https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/${station_vars.api_key}/aac_96/${item.local}/${item.duration}/playlist.m3u8?unique=website`;
      let url = `https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/${station_vars.hls}/aac_96/${item.local}/${item.duration}/playlist.m3u8?unique=website`;
      let title = `${results.data.name} - ${item.readable}`;

      let playerUrl = `url=${encodeURIComponent(
        url
      )}=&title=${encodeURIComponent(title)}`;
      if (item.timestamp > 1646548252) {
        div.append(
          `<a class="episode-row toggle-play" data-popup="/player?${playerUrl}"><i class="fa fa-play"></i> ${item.readable}</a>`
        );
      }
      return item;
    });
  }

  jQuery(".toggle-play").on("click", async function (e) {
    let popup = jQuery(this).data("popup");

    newwindow = window.open(
      `${window.location.origin}/${popup}`,
      "name",
      `height=700,width=700`
    );
    if (window.focus) {
      newwindow.focus();
    }
  });
};

const getProgramEpisodesReact = async (slug) => {
  const response = await fetch(
    `https://app.myradio.click/api/public/station/${station_vars.api_key}/program/${slug}?showEpisodes=true`
  );
  console.log("Run");
  const div = jQuery("#mrreactepisodes");
  const results = await response.json();

  if (results?.data?.episodes) {
    div.append("<h4 id='LatestEpisodes'>Latest Episodes</h4>");
    results.data.episodes.map((item, i) => {
      // let url = `https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/${station_vars.api_key}/aac_96/${item.local}/${item.duration}/playlist.m3u8?unique=website`;
      let url = `https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/${station_vars.hls}/aac_96/${item.local}/${item.duration}/playlist.m3u8?unique=website`;
      if (item.timestamp > 1646548252) {
        div.append(
          `<button class="mr-play-audio episode-row" data-title="${results?.data.name} - ${item.readable}" data-image="${results?.data?.image?.url}" data-url="${url}"><i class="fa fa-play"></i> ${item.readable}</button>`
        );
        // div.append(
        //   `<a href='/episodes/?date=${item.timestamp}'>Visit Link</a>`
        // );
      }
      return item;
    });
  }
  initReactPlayButtons();
};

const initReactPlayButtons = async () => {
  jQuery(".mr-play-audio").on("click", function (e) {
    e.preventDefault();
    console.log("here");
    if (!this.hasAttribute("data-title")) {
      var title = jQuery(this).text();
    } else {
      var title = jQuery(this).data("title");
    }
    var image = null;
    if (this.hasAttribute("data-image")) {
      image = jQuery(this).data("image");
    }
    if (!this.hasAttribute("data-url")) {
      load_audio(jQuery(this).attr("href"), title, image);
    } else {
      load_audio(jQuery(this).data("url"), title, image);
    }
  });
};
const initNotReactPlayButtons = async () => {
  jQuery(".mr-play-audio").on("click", function (e) {
    e.preventDefault(); 
    let popup = jQuery(this).data("popup");
    newwindow = window.open(
      `${window.location.origin}/${popup}`,
      "name",
      `height=700,width=700`
    );
    if (window.focus) {
      newwindow.focus();
    }
  });
};

const getGuide = async () => {
  // const response = await fetch(
  //   `https://app.myradio.click/api/public/station/${station_vars.api_key}/guide`
  // );
  const response = await fetch(
    `https://radio-online.nyc3.digitaloceanspaces.com/cached/stations/${station_vars.api_key}/${station_vars.api_key}-guide.json`
  );
  const results = await response.json();
  let tmp;

  let onAir = results.data.onair.slot;

  results.data.guide.forEach((slot) => {
    tmp = sortOutOffset(slot);
    jQuery(
      `.${tmp.weekday_start}_hour_${tmp.hour_start}_${slot.minute_start}`
    ).append(
      `<a href="/${slot.program.slug}" class="program_slot height_${
        slot.duration / 60
      } ${slot.id === onAir.id && "onair"}"><div>${slot.program.name}  
        </div></a>`
    );
    let ep = "";
    if (slot.episodes.length > 0) {
      let url = `https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/${station_vars.hls}/aac_96/${slot.episodes[0].local}/${slot.episodes[0].duration}/playlist.m3u8?unique=website`;
      if (station_vars?.react_or_not === "1") {
        ep = `<div class='mr-flex'><button class="list-episode-button mr-play-audio" data-title="${
          slot.program.name
        } - ${slot.episodes[0].readable}" ${
          slot.program.image ? `data-image="${slot.program.image.url}"` : ``
        } data-url="${url}"></button> Play Latest Episode - ${
          slot.episodes[0].readable
        }</div>`;
      } else {
        let tmpUrl = `https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/${station_vars.hls}/aac_96/${slot.episodes[0].local}/${slot.episodes[0].duration}/playlist.m3u8?unique=website`;
        let title = `${slot.program.name} - ${slot.episodes[0].readable}`;

        let playerUrl = `url=${encodeURIComponent(
          tmpUrl
        )}=&title=${encodeURIComponent(title)}`;
        ep = `<div class='mr-flex'><button class="list-episode-button mr-play-audio" data-popup="/player?${playerUrl}"></button> Play Latest Episode - ${slot.episodes[0].readable}</div>`;
      }
    }

    jQuery(`.weekday${tmp.weekday_start}`).append(
      `<div class='program-item-wrapper ${slot.id === onAir.id && "onair"}'>
			<div class='program-time'>${`${
        slot.hour_start >= 12 ? pad(slot.hour_start - 12) : pad(slot.hour_start)
      }:${pad(slot.minute_start)}`}${slot.hour_start >= 12 ? "pm" : "am"} </div>
			<div class='program-details'><a href="/${slot.program.slug}"><h4>${
        slot.id === onAir.id ? "<span class='online-alert'>ON AIR:</span> " : ""
      }${slot.program.name}</h4></a>
      <p class="mr-presenter">${
        slot.program.presenter_string &&
        `${`<span>Presented by</span>:${slot.program.presenter_string} `}`
      }</p>
				${slot.program.genre_string ? `<p>${slot.program.genre_string}</p>` : ""}
				${slot.program.introduction ? `<p>${slot.program.introduction}</p>` : ""} 
        ${ep}
			</div> 
			<div class='program-image'>${
        slot.program.image
          ? `<img src="${slot.program.image.url}" alt="${slot.program.name}" />`
          : ""
      }</div>
		</div>`
    );

    jQuery(`.mobile_weekday_${slot.weekday_start}`).append(
      `<a href="/${slot.program.slug}" class="program_slot_mobile ${
        slot.id === onAir.id && "onair"
      }"><div><span class='mobile-times'>${`${pad(slot.hour_start)}:${pad(
        slot.minute_start
      )}`} to ${`${pad(slot.hour_end)}:${pad(slot.minute_end)}`}</span> 
		<div class='mobile-program-name'>${
      slot.id === onAir.id && "<span class='online-alert'>ON AIR:</span> "
    }${slot.program.name}</div> 
		${
      slot.program.presenter_string &&
      `${`<span class='mobile-presenter'>Presented by ${slot.program.presenter_string}</span>`}`
    }</div></a>`
    );
  });
  let days = [];
  for (let index = 0; index < 7; index++) {
    days[index] = results.data.guide.filter(
      (item) => item.weekday_start === index
    );
  }
  jQuery(".programguide").removeClass("loading");
  if (station_vars?.react_or_not === "1") {
    initReactPlayButtons();
  } else {
    initNotReactPlayButtons();
  }
};

//   if (station_vars) {
//     if (guides.length > 0) {
//       getGuide();
//     } else {
//       console.log("Guide not found");
//     }
//     if (list.length > 0) {
//       getGuide();
//     } else {
//       console.log("List not found");
//     }
//     if (onairs.length > 0) {
//       getOnAir();
//     } else {
//       console.log("OnAir not found");
//     }
//     if (programDiv) {
//       console.log("program div exists apparently", programDiv);
//       let slug = jQuery("#mrepisodes").data("slug");
//       if (slug) getProgramEpisodes(slug);
//     }
//   } else {
//     console.log("Tjos [art");
//   }

function updateClick() {
  let dayNo = jQuery(this).data("day");
  jQuery(".weekday-toggle").removeClass("active");
  jQuery(this).addClass("active");
  jQuery(`.weekday-list`).removeClass("active");
  jQuery(`.weekday${dayNo}`).addClass("active");
}

const initMr = async () => {
  let guides = document.getElementsByClassName("programguide");
  let onairs = document.getElementsByClassName("onairnow");
  let list = document.getElementsByClassName("program-list");
  let programDiv = jQuery("#mrepisodes");
  let programReactDiv = jQuery("#mrreactepisodes");

  if (station_vars) {
    if (guides.length > 0) {
      getGuide();
    } else {
      console.log("Guide not found");
    }
    if (list.length > 0) {
      getGuide();
    } else {
      console.log("List not found");
    }
    if (onairs.length > 0) {
      getOnAir();
    } else {
      console.log("OnAir not found");
    }
    if (programDiv) {
      console.log("Found episode page");
      let slug = jQuery("#mrepisodes").data("slug");

      if (slug) getProgramEpisodes(slug);
    }
    if (programReactDiv) {
      console.log("Found episode page for react");
      let slug = jQuery("#mrreactepisodes").data("slug");

      if (slug) getProgramEpisodesReact(slug);
    }
  } else {
    console.log("Tjos [art");
  }
  jQuery(".weekday-toggle").on("click", updateClick);

  jQuery(".mr-play-audio").on("click", function (e) {
    e.preventDefault();
    console.log("here", station_vars);
    if (station_vars?.react_or_not === "1") {
      if (!this.hasAttribute("data-title")) {
        var title = jQuery(this).text();
      } else {
        var title = jQuery(this).data("title");
      }
      var image = null;
      if (this.hasAttribute("data-image")) {
        image = jQuery(this).data("image");
      }
      if (!this.hasAttribute("data-url")) {
        load_audio(jQuery(this).attr("href"), title, image);
      } else {
        load_audio(jQuery(this).data("url"), title, image);
      }
    } else {
      let popup = jQuery(this).data("popup");
      newwindow = window.open(
        `${window.location.origin}/${popup}`,
        "name",
        `height=700,width=700`
      );
      if (window.focus) {
        newwindow.focus();
      }
    }
  });
};

initMr();
