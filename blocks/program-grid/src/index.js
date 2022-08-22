/**
 * WordPress dependencies
 */
import { registerBlockType } from "@wordpress/blocks";
import { useBlockProps } from "@wordpress/block-editor";

// Register the block
registerBlockType("myradio-click/program-grid", {
  apiVersion: 2,
  edit: function () {
    const blockProps = useBlockProps();
    return (
      <div id="episode-list" class="episode-list" {...blockProps}>
        <h3>Your Program Grid will appear here</h3>
      </div>
    );
  },
  save: function () {
    return (
      <div id="mr-program-guide" class="programguide mr-loading">
        <span class="load">Loading Program Guide</span>
        <div className="desktop-program-grid"></div>
        <div className="mobile-program-grid"></div>
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
