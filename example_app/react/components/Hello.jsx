export default class Hello extends React.Component {


    render() {
        return <h3>Hello {this.props.name || ""}</h3>
    }
}