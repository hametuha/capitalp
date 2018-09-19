/**
 * Description
 */

/*global wp: false*/

(() => {

  let RichText = wp.editor.RichText;

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
      },
    },
    edit: ( { attributes, setAttributes } ) => {
      let onChangeContent = ( newContent ) => {
        setAttributes( { content: newContent } );
      };
      return (
        <div className={'bubble'}>
          <RichText tagName={'p'} className={'bubble'} value={attributes.content} onChange={onChangeContent}></RichText>
        </div>
      );
    },
    save: ( { attributes } ) => {
      return <div className={'bubble'}><p>{ attributes.content }</p></div>
    },
  } );

})();
