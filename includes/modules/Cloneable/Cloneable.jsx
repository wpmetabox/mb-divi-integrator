// External Dependencies
import React, { Component } from 'react';

class Cloneable extends Component {
  static slug = 'mbdi_cloneable';

  render() {
    // const layout = this.props.layout;
    // const field = this.props.field;

    return (
      <div className="mbdi-cloneable">
        <div className="mbdi-cloneable__title">Cloneable</div>
        <div className="mbdi-cloneable__content"></div>
      </div>
    );
  }
}

export default Cloneable;
