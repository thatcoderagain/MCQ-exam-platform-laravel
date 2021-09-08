const RawHTML = ({children, className = ""}) => {
    return (
        <div className={className}
          dangerouslySetInnerHTML={{ __html: children.replace(/\n/g, '')}} />
    );
};

export default RawHTML;
