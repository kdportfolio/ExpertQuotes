/* This section of the code registers a new block, sets an icon and a category, and indicates what type of fields it'll include. */

/**
* Expert Quote
*
*/  
wp.blocks.registerBlockType('expert-quote/expert-quote-block', {
  title: 'Expert Quote',
  icon: 'groups',
  category: 'common',
  attributes: {
    shortcode: {
      type: 'string',
      default : '[expert_quote]'},
  },
  
/* This configures how the content and color fields will work, and sets up the necessary elements */
  
  edit: function(props) {
    function updateContent(event) {
      props.setAttributes({shortcode: event.target.value})
    }
    return React.createElement(
      "div",
      null,
      React.createElement(
        "p",
        null,
        props.attributes.shortcode
      ),
    //   React.createElement("input", { type: "text", value: props.attributes.shortcode, onChange: updateContent }),
    );
  },
  save: function(props) {
    return React.createElement(
      "div",
      null,
      React.createElement(
        "p",
        null,
        props.attributes.shortcode
      ),
    );
  }
})