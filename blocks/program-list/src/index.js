/**
 * WordPress dependencies
 */
import { registerBlockType } from "@wordpress/blocks";

// Register the block
registerBlockType("myradio-click/program-list", {
  edit: function () {
    let $weekdays = [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
    ];

    const d = new Date();
    let day = d.getDay();

    return (
      <div id="program-list" class="program-list">
        <div class="program-list-wrapper">
          <div class="days-list">
            <ul class="weekday-toggles">
              {$weekdays.map((item, i) => (
                <li key={`listeitem${i}`}>
                  <button
                    class={`weekday-toggle weekday-toggle${i} ${
                      i === day ? "active" : ""
                    }`}
                    data-day={i}
                  >
                    {item}
                  </button>
                </li>
              ))}
            </ul>
          </div>
          <div class="program-list-programs">
            `
            {$weekdays.map((item, i) => (
              <div
                key={`dayitem${i}`}
                class={`weekday-list ${i === day ? "active" : ""} weekday${i}`}
              >
                {" "}
              </div>
            ))}
          </div>
        </div>
      </div>
    );
  },
  save: function () {
    let $weekdays = [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
    ];

    const d = new Date();
    let day = d.getDay();
    return (
      <div id="program-list" class="program-list">
        <div class="program-list-wrapper">
          <div class="days-list">
            <ul class="weekday-toggles">
              {$weekdays.map((item, i) => (
                <li key={`listeitem${i}`}>
                  <button
                    class={`weekday-toggle weekday-toggle${i} ${
                      i === day ? "active" : ""
                    }`}
                    data-day={i}
                  >
                    {item}
                  </button>
                </li>
              ))}
            </ul>
          </div>
          <div class="program-list-programs">
            `
            {$weekdays.map((item, i) => (
              <div
                key={`dayitem${i}`}
                class={`weekday-list ${i === day ? "active" : ""} weekday${i}`}
              >
                {" "}
              </div>
            ))}
          </div>
        </div>
      </div>
    );
  },
});

//  /**
//  * WordPress dependencies
//  */
//   import { registerBlockType } from '@wordpress/blocks';

//   /**
//    * Internal dependencies
//    */
//   import json from '../block.json';
//   import edit from './edit';
//   import save from './save';
//  //  import '../style.css';

//   // Destructure the json file to get the name and settings for the block
//   // For more information on how this works, see: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Destructuring_assignment
//   const { name } = json;

//   // Register the block
//   registerBlockType( name, {
//       edit, // Object shorthand property - same as writing: edit: edit,
//       save, // Object shorthand property - same as writing: save: save,
//   } );
