/**
 * Description
 */

/*global wp: false*/
/*global CapitalpInterview: false*/

(() => {



  const { RichText, InspectorControls }   = wp.editor;
  const { PanelBody, SelectControl } = wp.components;
  const { Fragment, Component } = wp.element;
  const options = CapitalpInterview.users.map(function(user){
    return {
      label: user.display_name,
      value: parseInt(user.ID),
    }
  });

  wp.blocks.registerBlockType( 'capitalp/interview', {
    title: 'インタビュー',
    description: '著者の発言を入力するブロックです。',
    category: 'embed',
    icon: 'format-chat',
    attributes: {
      content: {
        source: 'text',
        selector: 'p',
      },
      user: {
        type: 'integer',
        default: 0,
      },
    },
    edit: ( { attributes, setAttributes } ) => {
      const onChangeContent = ( newContent ) => {
        setAttributes( { content: newContent } );
      };
      return (
        <Fragment>
          <InspectorControls>
            <PanelBody title={ 'ユーザー' }>
              <SelectControl
                label={'ユーザーID'} value={attributes.user}
                options={options} onChange={(id) => { setAttributes({user: id}) }}></SelectControl>
            </PanelBody>
          </InspectorControls>
          <div className={'bubble'}>
            <RichText tagName={'p'} className={'bubble'} value={attributes.content} onChange={onChangeContent}></RichText>
          </div>
        </Fragment>
      );
    },
    save: ( { attributes } ) => {
      return <div className={'bubble'}><p>{attributes.content}</p></div>;
    },
  } );

})();
