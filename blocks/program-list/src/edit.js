/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { RichText, MediaUpload, useBlockProps } from "@wordpress/block-editor";
import apiFetch from "@wordpress/api-fetch";
import { Button } from "@wordpress/components";
const { useSelect } = wp.data;

const Edit = (props) => {
  const {
    element: { useState },
  } = wp;
  const [programs, setPrograms] = useState(null);
  const { somePosts } = useSelect((select) => {
    return {
      somePosts: select("core").getEntityRecords("postType", "program"),
    };
  });
  console.log("Posts from useSelect = ", somePosts);
  const {
    attributes: { title, mediaID, mediaURL, ingredients, instructions },
    setAttributes,
  } = props;

  const posts = apiFetch({ path: "/wp/v2/program?per_page=100" }).then(
    (posts) => {
      console.log(posts);
      setPrograms(posts);
    }
  );

  const blockProps = useBlockProps();

  const onChangeTitle = (value) => {
    setAttributes({ title: value });
  };

  const onSelectImage = (media) => {
    setAttributes({
      mediaURL: media.url,
      mediaID: media.id,
    });
  };
  const onChangeIngredients = (value) => {
    setAttributes({ ingredients: value });
  };

  const onChangeInstructions = (value) => {
    setAttributes({ instructions: value });
  };

  return (
    <div {...blockProps}>
      <RichText
        tagName="h2"
        placeholder={__("Write Recipe title…", "gutenberg-examples")}
        value={title}
        onChange={onChangeTitle}
      />
      <div className="recipe-image">
        <MediaUpload
          onSelect={onSelectImage}
          allowedTypes="image"
          value={mediaID}
          render={({ open }) => (
            <Button
              className={mediaID ? "image-button" : "button button-large"}
              onClick={open}
            >
              {!mediaID ? (
                __("Upload Image", "gutenberg-examples")
              ) : (
                <img
                  src={mediaURL}
                  alt={__("Upload Recipe Image", "gutenberg-examples")}
                />
              )}
            </Button>
          )}
        />
      </div>
      <h3>{__("Ingredients", "gutenberg-examples")}</h3>
      <RichText
        tagName="ul"
        multiline="li"
        placeholder={__("Write a list of ingredients…", "gutenberg-examples")}
        value={ingredients}
        onChange={onChangeIngredients}
        className="ingredients"
      />
      <h3>{__("Instructions", "gutenberg-examples")}</h3>
      <RichText
        tagName="div"
        multiline="p"
        className="steps"
        placeholder={__("Write the instructions…", "gutenberg-examples")}
        value={instructions}
        onChange={onChangeInstructions}
      />
    </div>
  );
};

export default Edit;
